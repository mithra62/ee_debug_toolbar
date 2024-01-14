<?php
namespace DebugToolbar\Errors\Services;

use DebugToolbar\Errors\Exceptions\ErrorException;
use DebugToolbar\Errors\Traits\LoggerTrait;

class ErrorHandlerService
{
    use LoggerTrait;

    /**
     * Whether to discard any existing page output before error display. Defaults to true.
     * @var bool
     */
    protected bool $discard_existing_output = true;

    /**
     * The size of the reserved memory. A portion of memory is pre-allocated so that
     * when an out-of-memory issue occurs, the error handler is able to handle the error with
     * the help of this reserved memory. If you set this value to be 0, no memory will be reserved.
     * Defaults to 256KB.
     * @var int
     */
    protected int $memory_reserve_size = 262144;

    /**
     * @var \Exception the exception that is being handled currently.
     */
    public $exception;

    /**
     * @var string Used to reserve memory for fatal error handler.
     */
    private $memory_reserve;

    /**
     * @var \Exception from HHVM error that stores backtrace
     */
    private $hhvm_exception;

    protected $settings;

    public function __construct()
    {
        $this->settings = ee('ee_debug_toolbar:SettingsService')->getSettings();
    }

    /**
     * Returns the output mode for the Error Handler
     * @return string
     */
    protected function getOutputMode(): string
    {
        if(ee()->input->is_ajax_request()) {
            return 'json';
        }

        if(PHP_SAPI === 'cli') {
            return 'cli';
        }

        return 'http';
    }

    /**
     * @return $this
     */
    public function register(): ErrorHandlerService
    {
        ini_set('display_errors', false);
        set_exception_handler([$this, 'handleException']);

        if (defined('HHVM_VERSION')) {
            set_error_handler([$this, 'handleHhvmError']);
        } else {
            set_error_handler([$this, 'handleError']);
        }

        if ($this->memory_reserve_size > 0) {
            $this->memory_reserve = str_repeat('x', $this->memory_reserve_size);
        }

        register_shutdown_function([$this, 'handleFatalError']);
        return $this;
    }

    /**
     * Unregisters this error handler by restoring the PHP error and exception handlers.
     * @return ErrorHandlerService
     */
    public function unregister()
    {
        restore_error_handler();
        restore_exception_handler();
        return $this;
    }

    /**
     * Outputs an Exception error
     * @codeCoverageIgnore
     * @param $exception
     */
    public function renderException($exception)
    {
        $msg = "An Error occurred:";
        $msg .= (string)$exception;
        if ($this->getDebugMode()) {
            if (PHP_SAPI === 'cli') {
                echo $msg . "\n";
            } else {

                switch ($this->getOutputMode()) {
                    case 'json':
                        header('Content-Type: application/problem+json');
                        $options = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
                        $data = ['title' => 'Critical Error', 'status' => 500, 'detail' => htmlspecialchars($msg, ENT_QUOTES)];
                        echo json_encode($data, $options);
                        exit;
                        break;


                    case 'cli':

                        break;

                    default:

                        show_exception($exception);
                        break;
                }


            }
        } else {
            show_error('An internal server error occurred.');
        }
    }

    /**
     * Handles uncaught PHP exceptions.
     *
     * This method is implemented as a PHP exception handler.
     *
     * @param \Exception $exception the exception that is not caught
     */
    public function handleException($exception)
    {
        $this->exception = $exception;

        // disable error capturing to avoid recursive errors while handling exceptions
        $this->unregister();

        // set preventive HTTP status code to 500 in case error handling somehow fails and headers are sent
        // HTTP exceptions will override this value in renderException()
        if (PHP_SAPI !== 'cli') {
            http_response_code(500);
        }

        try {
            $this->logger()->error($exception);
            log_message('error', $exception, true);
            if ($this->discard_existing_output) {
                $this->clearOutput();
            }

            $this->renderException($exception);
            if ($this->getDebugMode()) {
                if (defined('HHVM_VERSION')) {
                    flush();
                }
                exit(1);
            }

        } catch (\Exception $e) {
            // an other exception could be thrown while displaying the exception
            $msg = "An Error occurred while handling another error:\n";
            $msg .= (string)$e;
            $msg .= "\nPrevious exception:\n";
            $msg .= (string)$exception;
            if ($this->getDebugMode()) {
                if (PHP_SAPI === 'cli') {
                    echo $msg . "\n";
                } else {
                    echo '<pre>' . htmlspecialchars($msg, ENT_QUOTES) . '</pre>';
                }
            } else {
                echo 'An internal server error occurred.';
            }
            $msg .= "\n\$_SERVER = " . print_r($_SERVER, true);
            $this->logger()->error($msg);
            log_message('error', $exception, true);
            if (defined('HHVM_VERSION')) {
                flush();
            }
            exit(1);
        }
        $this->exception = null;
    }

    /**
     * Handles HHVM execution errors such as warnings and notices.
     *
     * This method is used as a HHVM error handler. It will store exception that will
     * be used in fatal error handler
     *
     * @param integer $code the level of the error raised.
     * @param string $message the error message.
     * @param string $file the filename that the error was raised in.
     * @param integer $line the line number the error was raised at.
     * @param mixed $context
     * @param mixed $backtrace trace of error
     * @return boolean whether the normal error handler continues.
     *
     * @throws ErrorException
     */
    public function handleHhvmError($code, $message, $file, $line, $context, $backtrace)
    {
        if ($this->handleError($code, $message, $file, $line)) {
            return true;
        }
        if (E_ERROR & $code) {
            $exception = new ErrorException($message, $code, $code, $file, $line);
            $ref = new \ReflectionProperty('\Exception', 'trace');
            $ref->setAccessible(true);
            $ref->setValue($exception, $backtrace);
            $this->_hhvmException = $exception;
        }
        return false;
    }

    /**
     * Handles PHP execution errors such as warnings and notices.
     *
     * This method is used as a PHP error handler. It will simply raise an [[ErrorException]].
     *
     * @param integer $code the level of the error raised.
     * @param string $message the error message.
     * @param string $file the filename that the error was raised in.
     * @param integer $line the line number the error was raised at.
     * @return boolean whether the normal error handler continues.
     *
     * @throws ErrorException
     */
    public function handleError($code, $message, $file, $line)
    {
        $general_error_codes = $this->settings['hide_error_codes'];
        $error = "\nCode: $code\n";
        $error .= "Message: $message\n";
        $error .= "File: $file:$line";
        if (in_array($code, $general_error_codes)) {
            $this->logger()->error($error);
            log_message('error', $error, true);
            $this->register(); //we reset the error handler just in case
            return false;
        }

        if (error_reporting() & $code) {

            $this->logger()->error($error);
            log_message('error', $error, true);
            $exception = new ErrorException($message, $code, $code, $file, $line);
            // in case error appeared in __toString method we can't throw any exception
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_shift($trace);
            foreach ($trace as $frame) {
                if ($frame['function'] === '__toString') {
                    $this->handleException($exception);
                    if (defined('HHVM_VERSION')) {
                        flush();
                    }
                    exit(1);
                }
            }
            throw $exception;
        }
        return false;
    }

    /**
     * Handles fatal PHP errors
     */
    public function handleFatalError()
    {
        unset($this->memory_reserve);

        $error = error_get_last();
        if (ErrorException::isFatalError($error)) {
            if (!empty($this->_hhvmException)) {
                $exception = $this->_hhvmException;
            } else {
                $exception = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
            }
            $this->exception = $exception;
            $this->logger->error($error);
            log_message('error', $error, true);
            if ($this->discard_existing_output) {
                $this->clearOutput();
            }
            $this->renderException($exception);
            // need to explicitly flush logs because exit() next will terminate the app immediately
            if (defined('HHVM_VERSION')) {
                flush();
            }
            exit(1);
        }
    }

    /**
     * Removes all output echoed before calling this method.
     */
    public function clearOutput()
    {
        // the following manual level counting is to deal with zlib.output_compression set to On
        for ($level = ob_get_level(); $level > 0; --$level) {
            if (!@ob_end_clean()) {
                ob_clean();
            }
        }
    }

    /**
     * @return int
     */
    public function getDebugMode()
    {
        return DEBUG;
    }
}
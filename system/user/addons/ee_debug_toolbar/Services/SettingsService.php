<?php

namespace Mithra62\DebugToolbar\Services;

class SettingsService
{
    /**
     * @var array
     */
    protected array $_defaults = [
        'theme' => 'default',
        'toolbar_position' => 'bottom-left',
        'cache_path' => '',
        'profile_exts' => [
            'js',
            'css',
            'jpg',
            'jpeg',
            'gif',
            'png',
            'bmp',
            'pdf',
            'svg',
            'htm',
            'html',
            'xhtml',
            'csv',
            'rss',
            'atom',
            'xml'
        ]
    ];

    /**
     * @var string
     */
    protected string $settings_table = 'ee_debug_toolbar_settings';

    /**
     * Returns the value straight from the database
     * @param string $setting
     */
    public function getSetting(string $key)
    {
        return ee()->db->get_where($this->settings_table, ['setting_key' => $key])->result_array();
    }

    /**
     * @param string $setting
     * @return mixed
     */
    public function addSetting(string $setting)
    {
        $data = [
            'setting_key' => $setting,
            'setting_value' => ''
        ];

        return ee()->db->insert($this->settings_table, $data);
    }

    /**
     * @param $key
     * @return bool
     */
    protected function isSetting($key)
    {
        if(array_key_exists($key, $this->_defaults)) {
            if(!$this->getSetting($key)) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @param string $key
     * @param string $value
     * @return false|void
     */
    public function updateSetting(string $key, string $value)
    {
        if(!$this->isSetting($key)) {
            $this->addSetting($key);
        }

        $data = [];
        if(is_array($value)) {
            $value = serialize($value);
            $data['serialized '] = '1';
        }

        $data['setting_value'] = $value;
        ee()->db->where('setting_key', $key);
        ee()->db->update($this->settings_table, $data);
    }

    /**
     * @param array $data
     * @return void
     */
    public function updateSettings(array $data): void
    {
        foreach($data AS $key => $value)
        {
            $this->updateSetting($key, $value);
        }
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->_defaults;
    }

    /**
     * @param array $new_defaults
     * @return $this
     */
    public function setDefaults(array $new_defaults = []): SettingsService
    {
        foreach($new_defaults AS $key => $value)
        {
            $this->_defaults[$key] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        ee()->db->flush_cache();
        ee()->db->select('setting_key, setting_value, `serialized`');
        $query = ee()->db->get($this->settings_table);
        $_settings = $query->result_array();
        $settings = [];
        foreach($_settings AS $setting)
        {
            $settings[$setting['setting_key']] = ($setting['serialized'] == '1' ? unserialize($setting['setting_value']) : $setting['setting_value']);
        }

        //now check to make sure they're all there and set default values if not
        foreach ($this->_defaults AS $key => $value)
        {
            //setup the override check
            if(isset($this->config->config['ee_debug_toolbar'][$key])) {
                $settings[$key] = $this->config->config['ee_debug_toolbar'][$key];
            }

            //normal default check
            if(!isset($settings[$key])) {
                $settings[$key] = $value;
            }
        }

        return $settings;
    }
}
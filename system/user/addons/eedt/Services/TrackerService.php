<?php

namespace DebugToolbar\Services;

class TrackerService
{
    /**
     * @var array
     */
    protected array $templates = [];

    /**
     * @param array $template_info
     * @return $this
     */
    public function trackTemplate(array $template_info): TrackerService
    {
        $this->templates[] = $template_info;
        return $this;
    }

    /**
     * @return array
     */
    public function getAllTemplates(): array
    {
        return $this->templates;
    }
}
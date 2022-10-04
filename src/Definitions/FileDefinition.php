<?php

namespace Eawardie\DataGrid\Definitions;

use Illuminate\Contracts\Support\Arrayable;

class FileDefinition implements Arrayable
{
    private string $value;

    private string $icon = 'mdi-file';

    private string $tooltip;

    private bool $canOpen = false;

    private bool $openInNewTab = false;

    private bool $canDownload = false;

    public function __construct()
    {
    }

    public function value(string $value): FileDefinition
    {
        $this->value = $value;

        return $this;
    }

    public function icon(string $icon): FileDefinition
    {
        $this->icon = $icon;

        return $this;
    }

    public function tooltip(string $tooltip): FileDefinition
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function canOpen(bool $newTab = true): FileDefinition
    {
        $this->canOpen = true;
        $this->openInNewTab = $newTab;

        return $this;
    }

    public function canDownload(): FileDefinition
    {
        $this->canDownload = true;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'icon' => $this->icon,
            'tooltip' => $this->tooltip,
            'canOpen' => $this->canOpen,
            'openInNewTab' => $this->openInNewTab,
            'canDownload' => $this->canDownload,
        ];
    }
}

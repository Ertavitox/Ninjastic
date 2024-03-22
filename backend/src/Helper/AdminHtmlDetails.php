<?php

namespace App\Helper;

use App\Interface\IEntity;

class AdminHtmlDetails
{
    private array $data;
    private array $pagerData;

    public function __construct(private string $controllerName)
    {
        $this->controllerName = $controllerName;
        $this->data = [];
        $this->pagerData = [];
    }

    public function setDefault(string $actionName, string $url, string $pageTitle, array $error): void
    {
        $this->data = [
            'controller_name' => $this->controllerName,
            'action_name' => $actionName,
            'controller_url' => $url,
            'page_title' => $pageTitle,
            'error' => $error
        ];
    }

    public function setExtraParameter($key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function setPagerData(array $data): void
    {
        $this->pagerData = $data;
    }

    public function getData(): array
    {
        return array_merge($this->data, $this->pagerData);
    }
}

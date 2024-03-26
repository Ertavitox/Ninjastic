<?php

namespace App\Helper;

use Doctrine\ORM\Tools\Pagination\Paginator;

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

    public function setPagerData($query, $currentPage = 1, $limit = 25): void
    {
        $this->pagerData = $this->adminPager($query, $currentPage, $limit);
    }

    public function getData(): array
    {
        return array_merge($this->data, $this->pagerData);
    }

    public function adminPager($query, $currentPage = 1, $limit = 25): array
    {
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pageCount = ceil($totalItems / $limit);
        $offset = ($limit * ($currentPage - 1));

        $paginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return array(
            'EntityList' => $paginator,
            'totalItems' => $totalItems,
            'pageCount' => $pageCount,
            'currentPage' => $currentPage,
            'limit' => $limit,
            'offset' => $offset,
            'query' => $_SERVER['QUERY_STRING'],
            "request_uri" => $_SERVER['REQUEST_URI'],
        );
    }

    public function checkStayPage()
    {
        if (isset($_POST['stay']) && $_POST['stay'] == 1) {
            return false;
        }
        return true;
    }
}

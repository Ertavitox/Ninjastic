<?php

namespace App\Twig;

use App\Helper\FlashBag;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Extension\GlobalsInterface;
use Twig\TwigTest;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getName()
    {
        return 'AppExtension';
    }

    public function getGlobals(): array
    {
        $session = new Session();
        return [
            'GET' => $_GET,
            'POST' => $_POST,
            'flashbag' => new FlashBag(),
            'session' => $session,
            'apps' => [
                'baseUrl' => $this->getBaseUrl(),
                'adminUrl' => $this->getAdminUrl(),
                "tableLength" => [
                    "10" => 10,
                    "25" => 25,
                    "50" => 50,
                    "100" => 100,
                ],
            ],
        ];
    }

    private function getBaseUrl(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $scheme = $request->getScheme();
        $host = $request->getHttpHost();
        return $scheme . '://' . $host;
    }

    private function getAdminUrl(): string
    {

        return $this->getBaseUrl() . "/admin";
    }



    public function getTests(): array
    {
        return [
            new TwigTest('numeric', [$this, 'isNumeric']),
            new TwigTest('number', [$this, 'isNumeric']),
        ];
    }

    public function isNumeric($value): bool
    {
        return is_numeric($value);
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getHostNameMain', array($this, 'getHostNameMain'), array('is_safe' => array('html'))),
            new TwigFunction('pathUrl', array($this, 'pathUrl'), array('is_safe' => array('html'))),
            new TwigFunction('checkStayPage', array($this, 'checkStayPage'), array('is_safe' => array('html'))),
        ];
    }

    public static function checkStayPage()
    {
        if (isset($_POST['stay']) && $_POST['stay'] == 1) {
            return false;
        }
        return true;
    }

    public static function adminPager($query, $currentPage = 1, $limit = 25)
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

    public function pathUrl($parameters = [])
    {
        $pagenumber = false;
        $pagenumberChange = false;

        $parseArray = array();
        if (isset($parameters['actPage'])) {
            $pagenumber = (int) $parameters['actPage'];
            $pagenumberChange = true;
        }
        $pageSize = false;
        $pageSizeChange = false;
        if (isset($parameters['pageSize'])) {
            $pageSize = (int) $parameters['pageSize'];
            $pageSizeChange = true;
        }
        $orderField = false;
        $orderFieldChange = false;
        $orderSortChange = false;
        if (isset($parameters['orderField'])) {
            $orderField = $parameters['orderField'];
            $orderFieldChange = true;
            $orderSortChange = true;
        }

        //Akt QueryString
        if (isset($parameters['q'])) {
            $parseArray = explode('&', $parameters['q']);
            if ($parseArray && is_array($parseArray) && count($parseArray) > 0) {
                foreach ($parseArray as $key => $item) {
                    if (strpos($item, 'actPage') !== false && $pagenumberChange) {
                        $parseArray[$key] = 'actPage=' . $pagenumber;
                        $pagenumberChange = false;
                    }
                    if (strpos($item, 'pageSize') !== false && $pageSizeChange) {
                        $parseArray[$key] = 'pageSize=' . $pageSize;
                        $pageSizeChange = false;
                    }
                    if (strpos($item, 'orderField') !== false && $orderFieldChange) {
                        $orderFieldParse = explode('=', $item);
                        if (isset($orderFieldParse[1]) && $orderFieldParse[1] == $orderField) {
                            $orderSortChange = true;
                        }
                        $parseArray[$key] = 'orderField=' . $orderField;
                        $orderFieldChange = false;
                    }
                    if (strpos($item, 'orderSort') !== false && $orderSortChange) {
                        $orderSortParse = explode('=', $item);
                        if (isset($orderSortParse[1]) && $orderSortParse[1] == 'ASC') {
                            $parseArray[$key] = 'orderSort=DESC';
                        } elseif (isset($orderSortParse[1]) && $orderSortParse[1] == 'DESC') {
                            $parseArray[$key] = 'orderSort=ASC';
                        }
                        $orderSortChange = false;
                    }
                }
            }
        }
        if ($pagenumberChange) {
            $pagenumberChange = false;
            $parseArray[] = 'actPage=' . $pagenumber;
        }
        if ($pageSizeChange) {
            $pageSizeChange = false;
            $parseArray[] = 'pageSize=' . $pageSize;
        }
        if ($orderFieldChange) {
            $orderFieldChange = false;
            $parseArray[] = 'orderField=' . $orderField;
        }
        if ($orderSortChange) {
            $orderSortChange = false;
            $parseArray[] = 'orderSort=ASC';
        }
        $queryString = implode('&', $parseArray);

        if (isset($parameters['r'])) {
            $request_uri = $parameters['r'];
            $explodedRequestUri = explode("?", $request_uri);
            return $this->getBaseUrl() . $explodedRequestUri[0] . "?" . $queryString;
        }

        return $this->getAdminUrl() . '/index.php?' . $queryString;
    }

    public static function getHostNameMain()
    {
        return  'Ninjastic Admin';
    }
}

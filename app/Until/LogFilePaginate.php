<?php
declare(strict_types=1);

namespace App\Until;

use Hyperf\Collection\Collection;
use Hyperf\Context\ApplicationContext;
use Hyperf\HttpServer\Contract\RequestInterface;

class LogFilePaginate{

    private $config;
    private $pattern;
    private $path;
    private $maxSize = 20 * 1024 * 1024;
    private $detail;
    private $collection;
    private $container;
    private $class = [
        'debug'     => 'text-primary',
        'info'      => 'text-info',
        'notice'    => 'text-warning',
        'warning'   => 'text-warning',
        'error'     => 'text-danger',
        'critical'  => 'text-danger',
        'alert'     => 'text-danger',
        'emergency' => 'text-danger',
        'processed' => 'text-primary',
        'failed'    => 'text-danger',
    ];
    private $logLevel = [
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'info',
        'debug',
        'processed',
        'failed'
    ];

    public $paginate;
    public $paginateSize = 0;
    public $currentPage = 1;
    public $currentFileName = '';
    public $keyword = "";
    public $level = "";
    public $model = "";

    //日志定时任务，清理前10天的记录
    public function __construct() {
        $this->config   = [
            "path"    => BASE_PATH . "/runtime/logs/",
            "pattern" => "*.log",
            "size"    => 10
        ];
        $this->path = $this->config['path'];
        $this->pattern = $this->config['pattern'];
        $this->paginateSize = $this->config['size'];
        $this->container = ApplicationContext::getContainer()->get(RequestInterface::class);
    }


    public function list() {
        return $this->files();
    }

    public function paginate() {
        $param = $this->container->all();
        $this->currentFileName = $param['file'] ?? '';
        $this->level = $param['level'] ?? '';
        $this->keyword = $param['kwd'] ?? '';
        $this->model = $param['model'] ?? '';
        $this->currentFileName = $param['file'] ?? '';
        $this->currentPage = isset($param['page']) ? (int)$param['page'] : $this->currentPage;
        return $this->content()->render();
    }


    public function render() {
        $total = $this->detail->count();
        $totalPage = $total ? ceil($total / $this->paginateSize) : 1;
        $prev = $this->currentPage - 1 < 1 ? 1 : $this->currentPage - 1;
        $next = $this->currentPage + 1 >= $totalPage ? $totalPage : $this->currentPage + 1;
        $path = $this->container->getUri()->getPath();
        return [
            'current_page' => $this->currentPage,
            'data' => new Collection(array_slice($this->detail->toArray(), ($this->currentPage - 1) * $this->paginateSize, $this->paginateSize)),
            'first_page_url' => $path. '?page=1',
            'last_page' => $totalPage,
            'last_page_url' => $path. '?page='.$totalPage,
            'next_page_url' => $next > 1 ? $path. '?page='.$next : '',
            'path' => $path,
            'per_page' => $this->paginateSize,
            'prev_page_url' => $prev > 1 ? $path. '?page='.$prev : '',
            'total' => $total,
        ];
    }

    private function files() {
        $files = [];
        $filePattern  = sprintf("%s%s", $this->path, $this->pattern);
        if ($filePattern) {
            $collection = new Collection(glob($filePattern));
            if ($collection->isNotEmpty()) {
                $content = $collection->filter(function ($log) {
                    return filesize($log) < $this->maxSize;
                })->map(function ($log) {
                    return $this->fileName($log);
                })->unique();
                $files = $content;
            }
        }
        $files = (new Collection($files))->values()->toArray();
        rsort($files);
        return $files;
    }


    private function content() {
        $lineList = [];
        if($this->currentFileName){
            $fullPath = $this->path . $this->currentFileName;
            if (file_exists($fullPath)) {
                $content = $this->readFileLine($fullPath);
                $collection = (new Collection($content));
                if ($collection->isNotEmpty()) {
                    $collection->each(function ($content) use (&$lineList) {
                        $content = trim(strtolower($content));
                        foreach ($this->logLevel as $level) {
                            $match = $this->pregMatch($level, $content);
                            if (empty($match[4])) {
                                continue;
                            }
                            if ($this->keyword) {
                                preg_match("/$this->keyword/i", $match[4], $keyword);
                                if (empty($keyword)) {
                                    continue;
                                }
                            }
                            if ($this->level) {
                                if ($this->level !== $level) {
                                    continue;
                                }
                            }
                            if ($this->model) {
                                if ($this->model !== $match[3]) {
                                    continue;
                                }
                            }
//
                            $reg = [' {' => '=>{', ' [' => '=>['];
                            $log = explode('=>', str_replace(array_keys($reg), $reg, $match[4]));
                            $lineList[] = [
                                'context' => $match[3],
                                'level'   => $level,
                                "class"   => $this->class[$level],
                                'date'    => $match[1],
                                'name'    => $log[0],
                                'text'    => json_decode($log[1]),
//                                'txt'    => str_replace(["\r", "\n"], "", $match[4]),
//                                'in_file' => isset($current[5]) ? $match[5] : "",
                                'stack'   => preg_replace("/^\n*/", '', $content)
                            ];
                        }
                    });
                }
            }
        }
        $this->detail = (new Collection($lineList))->sortByDesc("date")->values();
        return $this;
    }


    private function fileName(string $logFile) {
        return substr($logFile, strrpos($logFile, "/") + 1);
    }

    private function pregMatch($level, $content) {
        preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)' . $level . ': (.*?)( in .*?:[0-9]+)?$/i', $content, $match);
        return $match;
    }

    private function readFileLine($fullPath) {
        $content = [];
        $handle  = fopen($fullPath, "r+");
        if (is_resource($handle)) {
            while (feof($handle) == false) {
                $line = fgets($handle);
                if ($line) {
                    $content[] = $line;
                }
            }
        }
        return $content;
    }





    public function getLogListTotal(): int {
        return $this->collection->count();
    }

    public function getDetailTotal(): int {
        return $this->detail->count();
    }

}
<?php

class Router
{
    private $founded;
    private $output;
    private $request;

    /**
     * @param mixed $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return mixed|string
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function __construct($request = null)
    {
        $request = $request ?? ($_SERVER['REQUEST_URI']);
        $this->request = explode('?', $request)[0];
    }

    public function version($version, $function)
    {
        if (preg_match('/^\/(' . $version . ')/', $this->request)) {
            $request = str_replace("/$version", "", $this->request);
            $this->request = $request;
            $output = $function();
            if ($output != null) {
                $this->output = $output;
            }
        }
    }

    private mixed $regexResult = [];

    public function path($regex, $method = null, $isRegex = false)
    {
        if ($isRegex) {
            $regex = '/(?i)' . str_replace(['/'], ['\/'], $regex) . '/s';
            preg_match_all("/:\w+/", $regex, $params);
            $regexOriginal = preg_replace("(:\w+)", "([a-zA-Z0-9-_:#@]+)", ($regex));
            $regexVerify = preg_replace("(:\w+)", "([a-zA-Z0-9-_:#@]+)", strtolower($regex));
        }

        if ((($isRegex && preg_match($regexVerify, strtolower($this->request))) || (!$isRegex && strtolower($regex) == strtolower($this->request))) && !$this->founded) {
            if ($isRegex)
                preg_match($regexOriginal, ($this->request), $matches);
//            var_dump($this->request . " -> " . $regexOriginal ." -> ". $regexVerify , $matches);

            if ($method == null || $_SERVER['REQUEST_METHOD'] == $method || $_SERVER['IS_SOCKET'] == "YES") {
                if ($isRegex) {
                    unset($matches[0]);
                    foreach ($matches as $k => $v) {
                        $_GET[str_replace(":", "", $params[0][$k - 1])] = $v;
                    }
                    $this->regexResult = $matches;
                }
                return $this;
            }
        }
        return false;
    }

    public function load($function): static
    {
        $output = $function($this->regexResult);
        if ($output != null) {
            $this->output = $output;
        }
        $this->founded = true;
        return $this;
    }

    public function both($regex, $function, $middleware = null, $method = null, $isRegex = false): static
    {
//        var_dump( $this->request . " -> ". $regex  . '-> '. ($this->request == $regex) . "-> " . var_dump($this->found));
        $path = $this->path($regex, $method, $isRegex);
        if ($path !== false) {
            if ($middleware === null) {
                $path->load($function);
            } else {
                $mw = call_user_func($middleware);
                if ($mw === true) {
                    $path->load($function);
                } else {
                    $this->output = is_array($mw) ? $mw : ["code" => -100, "error" => "Authentication failed!"];
                    $this->founded = true;
                }
            }
        }
        return $this;
    }

    public function get($regex, $function, $middleware = null, $isRegex = false)
    {
        $this->both($regex, $function, $middleware, "GET", $isRegex);
    }

    public function post($regex, $function, $middleware = null, $isRegex = false)
    {
        $this->both($regex, $function, $middleware, "POST", $isRegex);
    }

    public function middleware($function, $onTrue)
    {
        $output = $function();
        if ($output === true) {
            $onTrue();
        } else {
            $this->output = is_array($output) ? $output : ["code" => -100, "error" => "Authentication failed!"];
            $this->displayOutput();
        }
    }

    public function displayOutput()
    {
        if ($this->getOutput() == "image") {
        } else if ($this->getOutput() != null) {
            header("Content-type: application/json");
            echo json_encode($this->getOutput(), 128);
        }
    }
}
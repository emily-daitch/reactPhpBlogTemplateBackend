<?php

namespace Api;

class Api
{
    public static function routing($current_link, $urls)
    {
        try
        {
            foreach($urls as $index => $url)
            {
                if($index != $current_link)
                {
                    continue;
                }

                $routeElement = explode('@', $url[0]);
                $className = $routeElement[0];
                $function = $routeElement[1];

                if(!file_exists("controllers/". $className . ".php"))
                {
                    return "Controller not found";
                }

                $class = "api\controllers\\$className";
                $object = new $class();

                $object->$function();
            }
        }
        catch(\Exception $e)
        {
            var_dump($e->getMessage());
        }
    }
}
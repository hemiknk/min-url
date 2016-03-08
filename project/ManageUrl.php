<?php

namespace Cut;

class ManageUrl
{
    public function showPage()
    {
        $shortLink = '';
        if ($uri = $this->getUrl()) {
            $link = Links::find(['shortLink' => $uri], ['shortLink', 'originalLink', 'toTime', 'id']);
            if ($link) {
                if (time() < $link->toTime) {
                    header("Location: $link->originalLink");
                    die;
                } else {
                    $link->delete();
                }
            } else {
                ob_start();
                include 'notFound.php';
                $page = ob_get_clean();
                echo $page;
                die;
            }
        } elseif (isset($_POST['link']) && $_POST['link'] != false) {
            $link = $_POST['link'];
            $shortUserLink = $_POST['userShLink'] ?? false;

            $shortLink = $shortUserLink;
            if (!$shortUserLink) {
                $shortLink = $this->generateUrl();
            }

            if (Links::find(['shortLink' => $shortLink], ['shortLink'])) {
                do {
                    $shortLink = $this->generateUrl();
                } while (Links::find(['shortLink' => $shortLink], ['shortLink']));
            }

            $linkInDb = new Links();
            $linkInDb->shortLink = $shortLink;
            $linkInDb->originalLink = $link;

            //if user input link time live
            if ($t = $this->getToTime()) {
                $linkInDb->toTime = $t;
            }

            $linkInDb->save();

            //echo this in mainView.php file
            $shortLink = $_SERVER['HTTP_REFERER'] . $shortLink;
        }

        ob_start();
        include 'mainView.php';
        $page = ob_get_clean();
        echo $page;
    }

    private function getToTime()
    {
        if ($toTime = strtotime($_POST['time'] ?? false)) {
            $needAdd = $toTime - strtotime("00:00:00");
            $timeToSave = time() + $needAdd;
            echo date('d-m-y H:i:s', $timeToSave) . " = timeToSave<br>";

            return $timeToSave;
        }
        return false;

    }

    private function generateUrl($length = 4)
    {
        $temp = str_replace(['.', '/', '\\'], '', password_hash($this->getUrl(), PASSWORD_DEFAULT));
        return substr($temp, -$length);
    }

    private
    function getUrl()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $res = ltrim($_SERVER['REQUEST_URI'], '/');
            return $res;
        }
        return false;
    }

    public
    function echoSome()
    {
        echo ' Some Text ';
    }

}
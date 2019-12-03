<?php

namespace App\Controller;

use phpseclib\Net\SSH2;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController {

    /**
     * @Route("/")
     */
    public function index(): Response {
        $ssh = new SSH2('hausy.janstepan.eu');
        if (!$ssh->login('haf', 'datalogger')) {
            exit('Login Failed');
        }

        $stream = $ssh->exec('ls zeman');
        $files = explode(PHP_EOL, $stream);
        $lastFile = null;
        foreach ($files as $key => $file) {
            if ($file !== '' && strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'csv') {
                $lastFile = $file;
            }
        }

        if ($lastFile) {
            $name = $lastFile;
            $csv = $ssh->exec('cat zeman/' . $name);
        } else {
            $name = null;
            $csv = '';
        }

        return $this->render(
            'index/index.html.twig',
            [
                'name' => $name,
                'csv' => $csv
            ]
        );
    }

}
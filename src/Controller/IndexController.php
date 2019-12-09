<?php

namespace App\Controller;

use App\Connectors\SSHConnector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController {

    /**
     * @var SSHConnector
     */
    protected $connector;

    /**
     * @param SSHConnector $connector
     */
    public function __construct(SSHConnector $connector) {
        $this->connector = $connector;
    }

    /**
     * @Route("/")
     */
    public function index(): Response {
        $ssh = $this->connector->getConnection();
        $folder = $this->getParameter('datalogger.folder');

        $stream = $ssh->exec('ls ' . $folder);
        $files = explode(PHP_EOL, $stream);
        $lastFile = null;
        foreach ($files as $key => $file) {
            if ($file !== '' && strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'csv') {
                $lastFile = $file;
            }
        }

        if ($lastFile) {
            $name = $lastFile;
            $csv = $ssh->exec('cat ' . $folder . '/' . $name);
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
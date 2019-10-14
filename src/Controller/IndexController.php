<?php

namespace App\Controller;

use FtpClient\FtpClient;
use FtpClient\FtpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController {

    /**
     * @Route("/")
     */
    public function index(): Response {
        $ftp = new FtpClient();
        try {
            $ftp->connect('imitgw.uhk.cz', false, 59703);
            $ftp->login('epsi', '2hzcW5FXL2');
        } catch (FtpException $e) {
            return new Response('Cannot handle connection to FTP server.', 500);
        }

        $ftp->pasv(true); // Set FTP mode passive, to create data connection from client, not from host
        $files = $ftp->scanDir('/1/');
        $lastFile = null;
        foreach ($files as $key => $file) {
            if ($file['type'] === 'file') {
                $lastFile = $file;
            }
        }

        if ($lastFile) {
            $name = $lastFile['name'];
            $csv = $ftp->getContent('/1/' . $name);
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
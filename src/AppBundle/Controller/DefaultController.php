<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use DiDom\Document;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $opts = array('http' =>
            array(
                'method'  => 'GET',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
            )
        );
        $context  = stream_context_create($opts);
        $html = file_get_contents('https://play.google.com/store/apps/details?id=ru.mail.mymusic', false, $context);

        $app = [];

        $document = new Document($html);
        $attributesRaw = $document->find('.meta-info');

        $attributes = [];
        foreach($attributesRaw as $item) {
            $attributes[] = [
                'attribute' => $item->find('.title')[0]->text(),
                'value' => $item->find('.content')[0]->text(),
            ];
        }
        $app['attributes'] = $attributes;


        $imagesRaw = $document->find('img.screenshot');
        $images = [];
        foreach($imagesRaw as $item) {
            $images[] = [
                'src' => $item->attr('src'),
                'title' => $item->attr('title'),
            ];
        }

        $app['images'] = $images;

        $app['desc'] = $document->find('.details-section.description')[0]->text();

        $app['cover'] = $document->find('.details-info img.cover-image')[0]->attr('src');
        $app['title'] = $document->find('.details-info .id-app-title')[0]->text();
        $app['author'] = [
            'title' => $document->find('.details-info .document-subtitle.primary span')[0]->text(),
            'uri' => $document->find('.details-info .document-subtitle.primary')[0]->attr('href'),
        ];
        $app['category'] = [
            'title' => $document->find('.details-info .document-subtitle.category span')[0]->text(),
            'uri' => $document->find('.details-info .document-subtitle.category')[0]->attr('href'),
        ];

        $app['rating'] = [
            'value' => $document->find('.details-section.reviews .rating-box meta[itemprop="ratingValue"]')[0]->attr('content'),
            'count' => $document->find('.details-section.reviews .rating-box meta[itemprop="ratingCount"]')[0]->attr('content'),
        ];


        dump($app); die;

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
}

<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {


        // replace this example code with whatever you need
        return $this->render('default/index.html.twig');
    }


    /**
     * @Route("/second", name="second")
     */


    public function testAction(Request $request)
    {
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('photo', FileType::class)
            ->add('submit', SubmitType::class)->getForm();



        $formView = $form->createView();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $form->getData();

            /*
                        $photo['photo']->move(
                            $this->getParameter('image'),
                            "photo.jpg"
                        );*/


            $this->resize($photo["photo"],$this->getParameter('image').pathinfo($photo["photo"]."_LARGE",PATHINFO_FILENAME),10, 200, 100);
            $this->resize($photo["photo"],$this->getParameter('image').pathinfo($photo["photo"]."_MEDIUM",PATHINFO_FILENAME),10, 100, 50);
            $this->resize($photo["photo"],$this->getParameter('image').pathinfo($photo["photo"]."_SMALL",PATHINFO_FILENAME),10,50,25);


            return $this->redirectToRoute('second');
        }
        return $this->render('default/second.html.twig', ['form'=>$formView]);
    }

    function resize($source, $destination, $quality, $maxWidth, $maxHeigth) {

        $info = getimagesize($source);
        $ratio = $info[0]/$info[1];


        $image = "";

        $newWidth;
        $newHeight;

        $maxWidth = $maxHeigth * $ratio;
        $maxHeigth = $maxWidth / $ratio;

        if ($maxWidth/$maxHeigth > $ratio) {
            $newWidth = $maxWidth*$ratio;
            $newHeight = $maxHeigth;
        } else {
            $newHeight = $maxWidth/$ratio;
            $newWidth = $maxWidth;
        }

        switch ($info['mime'] ){
            case 'image/jpeg':
                $image = \imagecreatefromjpeg($source);
                break;
            case 'image/gif':
                $image = \imagecreatefromgif($source);
                break;
            default:
                $image = \imagecreatefrompng($source);

        }


        $image = imagescale($image, $newWidth, $newHeight);


        imagejpeg($image, $destination, $quality);

    }


}

<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrère
 * Date: 21/07/14
 * Time: 12:52
 */
  /*
           ____________________
  __      /     ______         \
 {  \ ___/___ /       }         \
  {  /       / #      }          |
   {/ ô ô  ;       __}           |
   /          \__}    /  \       /\
<=(_    __<==/  |    /\___\     |  \
   (_ _(    |   |   |  |   |   /    #
    (_ (_   |   |   |  |   |   |
      (__<  |mm_|mm_|  |mm_|mm_|
*/

namespace Sites\Admin\Common\MediatekBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PdfController extends Controller
{
    public function indexAction()
    {
        $pdfs = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Document")->findAllAlphaOrdered("pdf");
        return $this->render("AdminCommonMediatekBundle:Pdf:index.html.twig", ["pdfs"=>$pdfs]);
    }

    public function editAction($id, Request $request)
    {

    }

    public function newAction()
    {

    }

    public function createAction($id, Request $request)
    {

    }

    public function updateAction($id, Request $request)
    {

    }

    public function deleteAction($id, Request $request)
    {

    }
} 

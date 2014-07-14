<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrère
 * Date: 14/07/14
 * Time: 08:56
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

namespace Sites\Admin\Common\MenuManagerBundle\Twig;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;
use Twig_SimpleFunction;

class MenuExtension extends Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return "menu_extension";
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction("generate_menu", [$this, "generateMenu"], ['is_safe' => ['html']])
        ];
    }

    public function generateMenu($menuName = "")
    {
        $html = "";

        if($this->container->has("doctrine") && $menuName != ""){
            $repo = $this->container->get("doctrine")->getRepository("AdminCommonMenuManagerBundle:Menu");
            $menu = $repo->findOneByTitle($menuName);
            if($menu){
                $html = $repo->childrenHierarchy(
                    $menu,
                    false,
                    [
                        "decorate"=>true,
                        "nodeDecorator"=>function($node){
                                return "<a href='".$node["link"]."'>".$node["title"]."</a>";
                            }
                    ]
                );
            }
        }
        return $html;
    }
}

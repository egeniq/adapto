<?php 

namespace Lesson1\Controller;

/**
 * Employee controller.
 */
class EmployeeController extends Adapto\Mvc\Controller\BaseController
{
    // basis configuratie van de controller, hier kun je acties registreren, overriden,
    // verwijderen, evt. de entityDef en/of form naam met een setter setten (ipv een property
    // zoals voorbeeld hierboven)
    protected function init()
    {
        parent::init();
        $this->removeAction('view');
        $this->addAction('edit', 'Adapto\Controller\Action\Edit', array('param' => 'value'));
    }

    // je kunt optioneel deze methode overriden en zelfs de hele entitydef met de base class
    // hier opbouwen, maar je kunt ook $_entityDefClass zetten met de class name of op basis van
    // een standaard locatie wordt het automatisch ontdekt, daarnaast als er geen gedefinieerd is
    // kan hij ook zelf op basis van de controller naam een wild guess doen
    protected function createEntityDef()
    {
        $def = parent::createEntityDef();
        $def->remove('created_at');
        return $def;
    }

    // eventueel te overriden om action handlers last-minute te configuren
    protected function createAction($name)
    {
        $action = parent::createAction($name);
        if ($name == 'edit') {
            $action->setX('y');
        }

        return $action;
    }

    // kun je in je controller overriden en implementeren, maar je kan het ook in een aparte class
    // doen, standaard implementatie doet het form instantieren op basis van de gespecificeerde
    // entity definition van de controller, of je kunt een expliciete benoemen in $_formClass, daarnaast
    // zouden we ook automatisch zoeken naar een form met een bepaalde naming scheme kunnen ondersteunen
    protected function createUIDef()
    {
        // form kan door verschillende acties worden gebruikt en is opvraagbaar door de acties
        // door controller->getUIDef aan te roepen deze doet evt. een createUIDef of geeft de bestaande
        // instantie terug
        $form = new \Adapto\UIDef\BaseUIDef($this->getEntityDef());
        $form->get('category', 'parent')->setReadonlyEdit(true)
        ->setHideList(true);
        $form->add(new \Adapto\UIDef\Widget\Dummy('test'))->insertAfter('name');
    }

    // $form->get geeft proxy terug die widget instanties bevat van de genoemde attributen
    // elke methode wordt op elk van de widget instanties aangeroepen, voor auto-completion zijn
    // de standaard methoden zoals readonly, hiden, tabs, sections etc. expliciet gedefinieerd in
    // proxy class maar door __call te implementeren kun je ook andere methoden aanroepen dus als
    // 2 attributen hebben van een bepaalde class met een methode die niet standaard is voor alle
    // widgets kun je ze toch voor die specifiek aanroepen, fluent interface om te chainen, indien
    // je get met een enkele widget of add doet krijg je direct de widget instantie terug en
    // roep je het daar op aan
}

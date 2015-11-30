<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Entity\Employee;


class MainController extends Controller
{
    
    const TIMER_INIT_VAL = 10*60; 


    /**
     * get timer value from session
     */
    private function getTimerValue(Session $session){
        
        // get last timer start timestamp, if null then initiate timer start
        $timer_stamp = $session->get('timer_stamp');
        if (!$timer_stamp){
            $timer_stamp = time();
            $session->set('timer_stamp', $timer_stamp);
        }

        $time_left = self::TIMER_INIT_VAL - (time() - $timer_stamp);
        $time_left = ($time_left < 0) ? 0 : $time_left;

        return $time_left;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        
        $session = $request->getSession();

        // get timer value from session
        $time_left = self::getTimerValue($session);
        if (!$time_left){
            $session->getFlashBag()->add('error', 'Нажаль час, відведений на заповнення форми, вийшов');
        }

        return $this->render('default/index.html.twig',
            array('time_left' => $time_left)
        );
    }

    /**
     * @Route("/api/addNewEmployee", name="_apiAddNewEmployee")
     */
    public function apiAddNewEmployeeAction(Request $request)
    {
        $session = $request->getSession();

        $time_left = self::getTimerValue($session);
        
        // if timer is not expired - add to db
        if ($time_left) {
            // add to db
            $employee = new Employee();
            $employee->setLastName($request->request->get('last_name'));
            $employee->setFirstName($request->request->get('first_name'));
            $employee->setMiddleName($request->request->get('middle_name'));
            $birth_date = \DateTime::createFromFormat('Y-m-d', $request->request->get('bdate'));
            $employee->setBirthDate($birth_date);

            $employee->setAddress($request->request->get('address'));
            $employee->setPhone($request->request->get('phone'));
            $employee->setEmail($request->request->get('email'));

            $employee->setBio($request->request->get('bio'));

            $em = $this->getDoctrine()->getManager();

            $em->persist($employee);
            $em->flush();
        }


        if (!$time_left){
            $response = new Response(json_encode(array('code' => 1, 'message' => 'Неможливо додати дані, так як вийшов час, відведений на заповнення форми')));
        } else {
            $response = new Response(json_encode(array('code' => 0, 'message' => 'Успішно виконано')));
        }

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}

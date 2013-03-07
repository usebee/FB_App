<?php

namespace CMS\Bundle\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * The contact class
 */
class ContactController extends Controller
{

    /**
     * index action
     *
     * @return type
     */
    public function indexAction()
    {
        return $this->render('CMSFrontBundle:Contact:index.html.twig');
    }

    /**
     * paris action
     *
     * @return type
     */
    public function parisAction()
    {
        return $this->render('CMSFrontBundle:Contact:paris.html.twig');
    }

    /**
     * show contact form popup
     *
     * @return type
     */
    public function contactPopupAction()
    {
        $request = $this->getRequest();
        $lang = $request->getLocale();

        if ($request->iCMSethod('POST')) {
            //send mail action
            $this->sendMail($request);
            $referer = $request->headers->get('referer');

            return $this->redirect($referer);
        }

        return $this->render(
            'CMSFrontBundle:Contact:form.html.twig', array('lang' => $lang)
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request request
     */
    private function sendMail(Request $request)
    {
        $emailGender = $request->get('quality');
        $emailName = $request->get('name');
        $emailFirstname = $request->get('first_name');
        $emailEmail = $request->get('e_mail');
        $emailCompany = $request->get('company');
        $emailPhone = $request->get('telephone');
        $emailComment = $request->get('comment');

        $message = \Swift_Message::newInstance()
            ->setSubject($this->get('translator')->trans('Contact email'))
            ->setFrom($emailEmail)
            ->setTo($this->container->getParameter('mailer_user'))
            ->setContentType('text/html')
            ->setBody(
                $this->renderView(
                    'CMSFrontBundle:Contact:email.html.twig', array(
                    'gender' => $emailGender,
                    'name' => $emailName,
                    'firstname' => $emailFirstname,
                    'email' => $emailEmail,
                    'company' => $emailCompany,
                    'phone' => $emailPhone,
                    'comment' => $emailComment,
                  ))
            );
        $this->get('mailer')->send($message);
    }

}

<?php
/*
 * (c) 2017: 975L <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Form\GiftVoucherAvailableType;
use c975L\GiftVoucherBundle\Service\GiftVoucherService;

class AvailableController extends Controller
{
//NEW
    /**
     * @Route("/gift-voucher/new-available",
     *      name="giftvoucher_new_available")
     * @Method({"GET", "HEAD", "POST"})
     */
    public function newAvailable(Request $request, GiftVoucherService $giftVoucherService)
    {
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Defines form
            $giftVoucher = new GiftVoucherAvailable();
            $giftVoucherConfig = array('action' => 'new');
            $form = $this->createForm(GiftVoucherAvailableType::class, $giftVoucher, array('giftVoucherConfig' => $giftVoucherConfig));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Adjust slug in case of not accepted signs
                $giftVoucher->setSlug($giftVoucherService->slugify($form->getData()->getSlug()));

                //Persists data in DB
                $em = $this->getDoctrine()->getManager();
                $em->persist($giftVoucher);
                $em->flush();

                //Redirects to the GiftVoucher created
                return $this->redirectToRoute('giftvoucher_display_available', array(
                    'id' => $giftVoucher->getId(),
                ));
            }

            return $this->render('@c975LGiftVoucher/forms/new.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//DISPLAY
    /**
     * @Route("/gift-voucher/display-available/{id}",
     *      name="giftvoucher_display_available",
     *      requirements={
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD"})
     */
    public function displayAvailable($id)
    {
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Gets the GiftVoucher
            $giftVoucher = $this->getDoctrine()
                ->getManager()
                ->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')
                ->findOneById($id);

            //Not existing GiftVoucher
            if (!$giftVoucher instanceof GiftVoucherAvailable) {
                throw $this->createNotFoundException();
            }

            return $this->render('@c975LGiftVoucher/pages/displayAvailable.html.twig', array(
                'giftVoucher' => $giftVoucher,
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//MODIFY
    /**
     * @Route("/gift-voucher/modify-available/{id}",
     *      name="giftvoucher_modify_available",
     *      requirements={
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function modifyAvailable(Request $request, GiftVoucherService $giftVoucherService, $id)
    {
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Gets the GiftVoucher
            $em = $this->getDoctrine()->getManager();
            $giftVoucher = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')->findOneById($id);

            //Not existing GiftVoucher
            if (!$giftVoucher instanceof GiftVoucherAvailable) {
                throw $this->createNotFoundException();
            }

            //Defines form
            $giftVoucherConfig = array('action' => 'modify');
            $form = $this->createForm(GiftVoucherAvailableType::class, $giftVoucher, array('giftVoucherConfig' => $giftVoucherConfig));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Adjust slug in case of not accepted signs
                $giftVoucher->setSlug($giftVoucherService->slugify($form->getData()->getSlug()));

                //Persists data in DB
                $em->persist($giftVoucher);
                $em->flush();

                //Redirects to the GiftVoucher
                return $this->redirectToRoute('giftvoucher_display_available', array(
                    'id' => $giftVoucher->getId(),
                ));
            }

            return $this->render('@c975LGiftVoucher/forms/modify.html.twig', array(
                'giftVoucher' => $giftVoucher,
                'form' => $form->createView(),
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//DUPLICATE
    /**
     * @Route("/gift-voucher/duplicate-available/{id}",
     *      name="giftvoucher_duplicate_available",
     *      requirements={
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function duplicateAvailable(Request $request, GiftVoucherService $giftVoucherService, $id)
    {
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Gets the GiftVoucher
            $em = $this->getDoctrine()->getManager();
            $giftVoucher = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')->findOneById($id);

            //Not existing GiftVoucher
            if (!$giftVoucher instanceof GiftVoucherAvailable) {
                throw $this->createNotFoundException();
            }

            //Defines form
            $giftVoucherClone = clone $giftVoucher;
            $giftVoucherClone
                ->setObject(null)
                ->setSlug(null)
            ;
            $giftVoucherConfig = array('action' => 'duplicate');
            $form = $this->createForm(GiftVoucherAvailableType::class, $giftVoucherClone, array('giftVoucherConfig' => $giftVoucherConfig));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Adjust slug in case of not accepted signs
                $giftVoucherClone->setSlug($giftVoucherService->slugify($form->getData()->getSlug()));

                //Persists data in DB
                $em->persist($giftVoucherClone);
                $em->flush();

                //Redirects to the GiftVoucher
                return $this->redirectToRoute('giftvoucher_display_available', array(
                    'id' => $giftVoucherClone->getId(),
                ));
            }

            //Returns the form to duplicate content
            return $this->render('@c975LGiftVoucher/forms/duplicate.html.twig', array(
                'form' => $form->createView(),
                'giftVoucher' => $giftVoucherClone,
                'object' => $giftVoucher->getObject(),
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//DELETE
    /**
     * @Route("/gift-voucher/delete-available/{id}",
     *      name="giftvoucher_delete_available",
     *      requirements={
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function deleteAvailable(Request $request, $id)
    {
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Gets the GiftVoucher
            $em = $this->getDoctrine()->getManager();
            $giftVoucher = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')->findOneById($id);

            //Not existing GiftVoucher
            if (!$giftVoucher instanceof GiftVoucherAvailable) {
                throw $this->createNotFoundException();
            }

            //Defines form
            $giftVoucherConfig = array('action' => 'delete');
            $form = $this->createForm(GiftVoucherAvailableType::class, $giftVoucher, array('giftVoucherConfig' => $giftVoucherConfig));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Persists data in DB
                $giftVoucher->setSuppressed(true);

                $em->persist($giftVoucher);
                $em->flush();

                //Redirects to the dashboard
                return $this->redirectToRoute('giftvoucher_dashboard');
            }

            return $this->render('@c975LGiftVoucher/forms/delete.html.twig', array(
                'form' => $form->createView(),
                'giftVoucher' => $giftVoucher,
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }
}
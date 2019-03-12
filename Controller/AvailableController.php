<?php
/*
 * (c) 2017: 975L <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Controller;

use c975L\ConfigBundle\Service\ConfigServiceInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Service\GiftVoucherAvailableServiceInterface;
use c975L\GiftVoucherBundle\Service\GiftVoucherPurchasedServiceInterface;
use c975L\ServicesBundle\Service\ServiceSlugInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * GiftVoucherAvailable Controller class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2017 975L <contact@975l.com>
 */
class AvailableController extends AbstractController
{
    /**
     * Stores GiftVoucherAvailableService
     * @var GiftVoucherAvailableServiceInterface
     */
    private $giftVoucherAvailableService;
    /**
     * Stores GiftVoucherPurchasedService
     * @var GiftVoucherPurchasedServiceInterface
     */
    private $giftVoucherPurchasedService;
    /**
     * Stores ServiceSlugInterface
     * @var ServiceSlugInterface
     */
    private $serviceSlug;

    public function __construct(
        GiftVoucherAvailableServiceInterface $giftVoucherAvailableService,
        GiftVoucherPurchasedServiceInterface $giftVoucherPurchasedService,
        ServiceSlugInterface $serviceSlug
    )
    {
        $this->giftVoucherAvailableService = $giftVoucherAvailableService;
        $this->giftVoucherPurchasedService = $giftVoucherPurchasedService;
        $this->serviceSlug = $serviceSlug;
    }

//DASHBOARD
    /**
     * Displays the dashboard
     * @return Response
     * @throws AccessDeniedException
     *
     * @Route("/gift-voucher/dashboard",
     *    name="giftvoucher_dashboard",
     *    methods={"HEAD", "GET"})
     */
    public function dashboard(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('c975LGiftVoucher-dashboard', null);

        //Gets GiftVouchers Available
        if ('available' == $request->query->get('v')) {
            $giftVouchers = $paginator->paginate(
                $this->giftVoucherAvailableService->getAll(),
                $request->query->getInt('p', 1),
                15
            );
        //Gets GiftVouchers Purchased
        } else  {
            $giftVouchers = $paginator->paginate(
                $this->giftVoucherPurchasedService->getAll(),
                $request->query->getInt('p', 1),
                15
            );
        }

        //Renders the dashboard
        return $this->render('@c975LGiftVoucher/pages/dashboard.html.twig', array(
            'giftVouchers' => $giftVouchers,
        ));
    }

//DISPLAY
    /**
     * Displays the GiftVoucherAvailable
     * @return Response
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     *
     * @Route("/gift-voucher/display/{id}",
     *    name="giftvoucher_display",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     */
    public function display(GiftVoucherAvailable $giftVoucherAvailable)
    {
        $this->denyAccessUnlessGranted('c975LGiftVoucher-display', $giftVoucherAvailable);

        //Renders the GiftVoucherAvailable
        return $this->render('@c975LGiftVoucher/pages/displayAvailable.html.twig', array(
            'giftVoucher' => $giftVoucherAvailable,
        ));
    }

//CREATE
    /**
     * Creates a GiftVoucherAvailable
     * @return Response
     * @throws AccessDeniedException
     *
     * @Route("/gift-voucher/create",
     *    name="giftvoucher_create",
     *    methods={"HEAD", "GET", "POST"})
     */
    public function create(Request $request)
    {
        $giftVoucherAvailable = new GiftVoucherAvailable();
        $this->denyAccessUnlessGranted('c975LGiftVoucher-create', $giftVoucherAvailable);

        //Defines form
        $form = $this->giftVoucherAvailableService->createForm('create', $giftVoucherAvailable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Registers the GiftVoucherAvailable
            $this->giftVoucherAvailableService->register($giftVoucherAvailable);

            //Redirects to the GiftVoucherAvailable
            return $this->redirectToRoute('giftvoucher_display', array(
                'id' => $giftVoucherAvailable->getId(),
            ));
        }

        //Renders the create form
        return $this->render('@c975LGiftVoucher/forms/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

//MODIFY
    /**
     * Modifies the GiftVoucherAvailable using its unique id
     * @return Response
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     *
     * @Route("/gift-voucher/modify/{id}",
     *    name="giftvoucher_modify",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "GET", "POST"})
     */
    public function modify(Request $request, GiftVoucherAvailable $giftVoucherAvailable)
    {
        $this->denyAccessUnlessGranted('c975LGiftVoucher-modify', $giftVoucherAvailable);

        //Defines form
        $form = $this->giftVoucherAvailableService->createForm('modify', $giftVoucherAvailable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Registers the GiftVoucherAvailable
            $this->giftVoucherAvailableService->register($giftVoucherAvailable);

            //Redirects to the GiftVoucher
            return $this->redirectToRoute('giftvoucher_display', array(
                'id' => $giftVoucherAvailable->getId(),
            ));
        }

        //Renders the modify form
        return $this->render('@c975LGiftVoucher/forms/modify.html.twig', array(
            'giftVoucher' => $giftVoucherAvailable,
            'form' => $form->createView(),
        ));
    }

//DUPLICATE
    /**
     * Duplicates the GiftVoucherAvailable using its unique id
     * @return Response
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     *
     * @Route("/gift-voucher/duplicate/{id}",
     *    name="giftvoucher_duplicate",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "GET", "POST"})
     */
    public function duplicate(Request $request, GiftVoucherAvailable $giftVoucherAvailable)
    {
        $this->denyAccessUnlessGranted('c975LGiftVoucher-duplicate', $giftVoucherAvailable);

        //Defines form
        $giftVoucherAvailableClone = $this->giftVoucherAvailableService->cloneObject($giftVoucherAvailable);
        $form = $this->giftVoucherAvailableService->createForm('duplicate', $giftVoucherAvailableClone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Registers the GiftVoucherAvailable
            $this->giftVoucherAvailableService->register($giftVoucherAvailableClone);

            //Redirects to the GiftVoucher
            return $this->redirectToRoute('giftvoucher_display', array(
                'id' => $giftVoucherAvailableClone->getId(),
            ));
        }

        //Returns the form to duplicate content
        return $this->render('@c975LGiftVoucher/forms/duplicate.html.twig', array(
            'form' => $form->createView(),
            'giftVoucher' => $giftVoucherAvailableClone,
            'object' => $giftVoucherAvailable->getObject(),
        ));
    }

//DELETE
    /**
     * Deletes the GiftVoucherAvailable using its unique id
     * @return Response
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     *
     * @Route("/gift-voucher/delete/{id}",
     *    name="giftvoucher_delete",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "GET", "POST"})
     */
    public function delete(Request $request, GiftVoucherAvailable $giftVoucherAvailable)
    {
        $this->denyAccessUnlessGranted('c975LGiftVoucher-delete', $giftVoucherAvailable);

        //Defines form
        $form = $this->giftVoucherAvailableService->createForm('delete', $giftVoucherAvailable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Marks as deleted the GiftVoucherAvailable
            $this->giftVoucherAvailableService->delete($giftVoucherAvailable);

            //Redirects to the dashboard
            return $this->redirectToRoute('giftvoucher_dashboard');
        }

        //Renders the delete form
        return $this->render('@c975LGiftVoucher/forms/delete.html.twig', array(
            'form' => $form->createView(),
            'giftVoucher' => $giftVoucherAvailable,
        ));
    }

//CONFIG
    /**
     * Displays the configuration
     * @return Response
     * @throws AccessDeniedException
     *
     * @Route("/gift-voucher/config",
     *    name="giftvoucher_config",
     *    methods={"HEAD", "GET", "POST"})
     */
    public function config(Request $request, ConfigServiceInterface $configService)
    {
        $this->denyAccessUnlessGranted('c975LGiftVoucher-config', null);

        //Defines form
        $form = $configService->createForm('c975l/giftvoucher-bundle');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Validates config
            $configService->setConfig($form);

            //Redirects
            return $this->redirectToRoute('giftvoucher_dashboard');
        }

        //Renders the config form
        return $this->render('@c975LConfig/forms/config.html.twig', array(
            'form' => $form->createView(),
            'toolbar' => '@c975LGiftVoucher',
        ));
    }

//SLUG
    /**
     * Returns the slug corresponding to the text provided
     * @return JsonResponse
     * @throws AccessDeniedException
     *
     * @Route("/gift-voucher/slug/{text}",
     *    name="giftvoucher_slug",
     *    methods={"POST"})
     * @Method({"POST"})
     */
    public function slug($text)
    {
        $this->denyAccessUnlessGranted('c975LGiftVoucher-slug', null);

        return $this->json(array('a' => $this->serviceSlug->slugify('c975LGiftVoucherBundle:GiftVoucherAvailable', $text)));
    }

//HELP
    /**
     * Displays the help
     * @return Response
     * @throws AccessDeniedException
     *
     * @Route("/gift-voucher/help",
     *    name="giftvoucher_help",
     *    methods={"HEAD", "GET"})
     */
    public function help()
    {
        $this->denyAccessUnlessGranted('c975LGiftVoucher-help', null);

        //Renders the help
        return $this->render('@c975LGiftVoucher/pages/help.html.twig');
    }
}

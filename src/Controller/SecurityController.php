<?php


namespace App\Controller;

use App\Form\RegisterFormType;
use App\Service\Security\SecurityService;
use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private SecurityService $securityService;

    /**
     * @param SecurityService $securityService
     */
    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * @Route("/security/login", name="app.security.login")
     *
     * @param AuthenticationUtils $authenticationUtils
     * @param SessionInterface    $session
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $session): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/security/register", name="app.security.register")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function register(Request $request): Response
    {
        $form = $this->createForm(RegisterFormType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user = $form->getData();
                $this->securityService->register($user);

                if ($this->securityService->requiredConfirmEmail()) {
                    return $this->render('security/confirm_email.html.twig');
                }

                return $this->securityService->login($request, $user);
            } catch (Exception $exception) {
                $form->addError(new FormError('Internal Server Error'));
            }
        }

        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/security/confirm/email", name="app.security.confirm_email")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function confirmEmail(Request $request): Response
    {
        if ($this->securityService->confirmEmail($request->get('email'), $request->get('code'))) {
            return $this->securityService->login($request, $this->securityService->getUser($request->get('email')));
        }

        throw new NotFoundHttpException();
    }

    /**
     * @Route("/security/logout", name="app.security.logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
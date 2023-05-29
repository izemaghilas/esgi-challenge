<?php

namespace App\Controller;

use App\Entity\Content;
use App\Entity\Purchase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class StripeController extends AbstractController
{

    public function __construct(
        private readonly string $stripe_publichable_key,
        private readonly string $end_point_secret,
        private readonly string $front_url,
        private readonly EntityManagerInterface $entityManager,
    ) {

    }


    #[Route('/api/stripe-session', name: 'stripe-session', methods: ['POST'])]
    public function createSession(Request $request)
    {
        \Stripe\Stripe::setApiKey($this->stripe_publichable_key);

        header('Content-Type: application/json');

        $data = json_decode($request->getContent(), true);

        $user_id = $data['userId'] ?? '';
        $content_id = $data['contentId'] ?? '';

        $success_url = $this->front_url . '/course' . '/' . $content_id;
        $cancel_url = $this->front_url . '/payment/cancel';

        //get the content from the database
        $content = $this->entityManager->getRepository(Content::class)->findOneBy(['id' => $content_id]);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $user_id]);

        if (!$content || !$content->getPrice() || $user === null) {
            throw new NotFoundHttpException();
        }

        if ($content->getCreatorId() === $user) {
            throw new BadRequestHttpException('course owner');
        }

        try {
            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'unit_amount' => $content->getPrice() * 100,
                            'product_data' => [
                                'name' => $content->getTitle(),
                            ],
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => $success_url,
                'cancel_url' => $cancel_url,
                'metadata' => [
                    'user_id' => $user_id,
                    'content_id' => $content_id
                ],
            ]);
        } catch (\UnexpectedValueException $e) {
            http_response_code(400);
        }

        return $this->json(['id' => $checkout_session->id]);
    }

    #[Route('/stripe-validate', name: 'app_stripe', methods: ['POST'])]
    public function validateSession(Request $request)
    {
        \Stripe\Stripe::setApiKey($this->stripe_publichable_key);

        $endpoint_secret = $this->end_point_secret;

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        switch ($event->type) {
            case 'checkout.session.completed':

                $content_id = $event->data->object->metadata->content_id;
                $user_id = $event->data->object->metadata->user_id;

                //get the content from the database
                $content = $this->entityManager->getRepository(Content::class)->findOneBy(['id' => $content_id]);

                if (!$content) {
                    throw new NotFoundHttpException();
                }

                $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $user_id]);

                if (!$user) {
                    throw new NotFoundHttpException();
                }

                $puchases = new Purchase();
                $puchases->setBuyer($user);
                $puchases->setCourse($content);

                $this->entityManager->persist($puchases);
                $this->entityManager->flush();

                return $this->json(['message' => "success"]);

            default:
                break;
        }

        return $this->json(['message' => "something went wrong"]);
    }
}
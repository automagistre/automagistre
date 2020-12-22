<?php

declare(strict_types=1);

namespace App\ATS\Controller;

use App\Calendar\Entity\EntryView;
use App\Customer\Entity\Person;
use App\Order\Entity\Order;
use App\Order\Enum\OrderStatus;
use App\Shared\Doctrine\Registry;
use DateTimeImmutable;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Money\Formatter\DecimalMoneyFormatter;
use function sprintf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class InteractiveController extends AbstractController
{
    private Registry $registry;

    private DecimalMoneyFormatter $moneyFormatterr;

    public function __construct(Registry $registry, DecimalMoneyFormatter $moneyFormatterr)
    {
        $this->registry = $registry;
        $this->moneyFormatterr = $moneyFormatterr;
    }

    /**
     * @Route("/callback/uiscom/interactive", name="uiscom_interactive", methods={"POST"})
     */
    public function __invoke(Request $request): Response
    {
        $data = $request->toArray();

        try {
            $phoneNumber = PhoneNumberUtil::getInstance()->parse('+'.$data['numa']);
        } catch (NumberParseException $e) {
            return new JsonResponse(['returned_code' => 1]);
        }

        $person = $this->registry->findBy(Person::class, ['telephone' => $phoneNumber]);
        if (!$person instanceof Person) {
            return new JsonResponse(['returned_code' => 1]);
        }

        $name = $person->getFirstname();
        if (null === $name) {
            return new JsonResponse(['returned_code' => 1]);
        }

        $message = $this->message($person);
        if ('' === $message) {
            return new JsonResponse([
                'returned_code' => 1,
                'text' => sprintf('Здраствуйте %s, пожалуйста дождитесь ответа оператора.', $name),
                'operator_text' => sprintf('%s %s', $person->getLastname() ?? '', $name),
            ]);
        }

        $text = sprintf(
            'Здраствуйте %s. %s Если у вас остались вопросы дождитесь ответа оператора.',
            $name,
            $message,
        );

        return new JsonResponse([
            'text' => $text,
        ]);
    }

    private function message(Person $person): string
    {
        $message = '';
        $scheduleInMessage = false;

        $entry = $this->registry->findBy(
            EntryView::class,
            ['orderInfo.customerId' => $person->toId()],
            ['id' => 'DESC'],
        );
        if ($entry instanceof EntryView) {
            $date = $entry->schedule->date;

            if ($date > new DateTimeImmutable()) {
                $scheduleInMessage = true;
                $message .= 'Вы записаны на ';

                if ($date->format('Y-m-d') === (new DateTimeImmutable())->format('Y-m-d')) {
                    $message .= 'сегодня ';
                } elseif ($date->format('Y-m-d') === (new DateTimeImmutable('+1 day'))->format('Y-m-d')) {
                    $message .= 'завтра ';
                } else {
                    $message .= sprintf(
                        '%s %s %s',
                        [
                            1 => 'понедельник',
                            2 => 'вторник',
                            3 => 'среду',
                            4 => 'четверг',
                            5 => 'пятницу',
                            6 => 'субботу',
                            7 => 'воскрсенье',
                        ][(int) $date->format('N')],
                        $date->format('d'),
                        [
                            1 => 'января',
                            2 => 'февраля',
                            3 => 'марта',
                            4 => 'апреля',
                            5 => 'мая',
                            6 => 'июня',
                            7 => 'июля',
                            8 => 'августа',
                            9 => 'сентября',
                            10 => 'октября',
                            11 => 'ноября',
                            12 => 'декабря',
                        ][(int) $date->format('n')],
                    );
                }

                $message .= sprintf(' к %s.', $date->format('H:i'));
            }
        }

        $order = $this->registry->findBy(Order::class, [
            'customerId' => $person->toId(),
            'status' => [
                OrderStatus::ordering(),
                OrderStatus::tracking(),
                OrderStatus::working(),
                OrderStatus::ready(),
                OrderStatus::scheduling(),
            ],
        ]);

        $orderStatusInMessage = false;
        if ($order instanceof Order) {
            $orderStatus = $order->getStatus();

            if (false === $scheduleInMessage) {
                switch (true) {
                    case $orderStatus->isOrdering():
                        $message .= 'По вашему заказу осуществляется заказ запчастей.';
                        $orderStatusInMessage = true;
                        break;
                    case $orderStatus->isTracking():
                        $message .= 'По вашему заказу ожидаются запчасти.';
                        $orderStatusInMessage = true;
                        break;
                    case $orderStatus->isWorking():
                        $message .= 'Работы по вашему автомобилю ещё не завершены.';
                        $orderStatusInMessage = true;
                        break;
                    case $orderStatus->isReady():
                        $message .= 'Ваш автомобиль готов и ожидает вас.';
                        $orderStatusInMessage = true;
                        break;
                }
            }

            if ($scheduleInMessage || $orderStatusInMessage) {
                $forPayment = $order->getTotalForPayment();
                if ($forPayment->isPositive()) {
                    $message .= sprintf(
                        ' К оплате %s рублей.',
                        $this->moneyFormatterr->format($forPayment),
                    );
                }
            }
        }

        return $message;
    }
}

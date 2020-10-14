<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Communication;
use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class AppFixtures
 */
class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 0; $i < 15; $i++) {
            $contact = new Contact();
            $contact->setName('Contact #' . $i);

            for ($j = random_int(0, 2); $j < 3; $j++) {
                $info = $this->getCommunicationInfo();

                $communication = new Communication();
                $communication->setName($info['name']);
                $communication->setValue($info['value']);
                $communication->setContact($contact);

                $manager->persist($communication);
            }

            $manager->persist($contact);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    private function getCommunicationInfo(): array
    {
        switch (rand(0, 2)) {
            case 0:
                $phone = sprintf('+375 (%s) %s-%s-%s', $this->getRandomFromArray(['29', '33']), $this->getRandomNumbers(3), $this->getRandomNumbers(2), $this->getRandomNumbers(2));

                return ['name' => 'phone', 'value' => $phone];
            case 1:
                $email = sprintf('%s@%s', substr(sha1(microtime()), 0, 8), $this->getRandomFromArray(['gmail.com', 'yandex.by', 'mail.ru']));

                return ['name' => 'email', 'value' => $email];
            case 2:
                $vk = sprintf('https://vk.com/id%s', $this->getRandomNumbers(8));

                return ['name' => 'vk', 'value' => $vk];
        }

        return ['none' => 'none'];
    }

    /**
     * @param int $count
     *
     * @return string
     */
    private function getRandomNumbers(int $count): string
    {
        $result = '';

        for ($i = 0; $i < $count; $i++) {
            $result .= rand(0, 9);
        }

        return $result;
    }

    /**
     * @param array $array
     *
     * @return mixed
     */
    public function getRandomFromArray(array $array)
    {
        return $array[rand(0, count($array) - 1)] ?? null;
    }
}

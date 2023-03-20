<?php
// src/Command/FetchFruitCommand.php
namespace App\Command;
use App\Entity\Fruit;
use Symfony\Component\Console\{
    Attribute\AsCommand, 
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface
};
use Symfony\Component\{
    Mailer\MailerInterface,
    Mailer\Transport,
    Mailer\Mailer,
    Mime\Email

};
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'app:fetch-fruit',
    description: 'Fetch fruits and save into db',
)]
class FetchFruitCommand extends Command 
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $client,
        private MailerInterface $mailer
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to fetch fruits and save into db');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->client->request(
            'GET',
            'https://fruityvice.com/api/fruit/all'
        );
        $output->writeln('Fetching fruits list...');
        $content = $response->getContent();
        $content = $response->toArray();
        foreach ($content as $key => $fruit) {
            $exist = $this->entityManager->getRepository(Fruit::class)->findOneBy([
                'genus' => $fruit['genus'],
                'name' => $fruit['name'],
            ]);
            if (!$exist) {
                $newFruit = new Fruit();
                $newFruit->setGenus($fruit['genus']);
                $newFruit->setName($fruit['name']);
                $newFruit->setFamily($fruit['family']);
                $newFruit->setForder($fruit['order']);
                $newFruit->setNutritions($fruit['nutritions']);
                $this->entityManager->persist($newFruit);
                $this->entityManager->flush();
                $output->writeln('Found new fruit <' . $fruit['name'] . '>. Saved into DB!');
                $this->sendEmail($fruit);
            }
        }
        $output->writeln('Done!');
        return Command::SUCCESS;
    }

    private function sendEmail($fruit) 
    {
        $email = (new Email())
            ->from('hello@fruit.com')
            ->to('arontaysk@gmail.com')
            ->subject('New fruit added!')
            ->text('New fruit added: '. $fruit['name'])
            ->html('<p>New fruit added: '. $fruit['name']. '</p>');
        try {
            $this->mailer->send($email);

        } catch (TransportExceptionInterface $e) {
            var_dump($e->getMessage());
        }
    }
}
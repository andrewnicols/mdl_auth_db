<?php

namespace AppBundle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Doctrine\Bundle\DoctrineBundle\Registry;

use AppBundle\Entity\Course;
use AppBundle\Entity\Person;

class CreateTestDataCommand extends ContainerAwareCommand {

    /**
     * @var Doctrine
     */
    private $doctrine;

    public function __construct(Registry $doctrine) {
        $this->doctrine = $doctrine;

        parent::__construct();
    }

    protected function configure() {
        $this
            ->setName('appbundle:createtestdata')
            ->setDescription('Create test data for Auth plugin')

            ->addArgument(
                'usercount',
                InputArgument::REQUIRED,
                'The number of test users to create'
            )

            ->addArgument(
                'coursecount',
                InputArgument::REQUIRED,
                'The number of test courses to create'
            )

            ->addArgument(
                'maxuserspercourse',
                InputArgument::REQUIRED,
                'The maximum number of users to insert per course'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('<info>Inserting courses</info>');
        $this->insertCourses($input, $output);
        $output->writeln('<info>done</info>');

        $output->writeln('<info>Inserting users</info>');
        $this->insertUsers($input, $output);
        $output->writeln('<info>done</info>');

        $em = $this->doctrine->getManager();
        $em->flush();
    }

    protected function insertCourses(InputInterface $input, OutputInterface $output) {
        if ($lastCourse = $this->doctrine->getRepository('AppBundle:Course')->findOneBy([], ['id' => 'DESC'])) {
            $startId = $lastCourse->getId();
        } else {
            $startId = 0;
        }
        $em = $this->doctrine->getManager();
        $courseCount = $startId + $input->getArgument('coursecount');

        for ($i = $startId; $i < $courseCount; $i++) {
            $course = new Course();
            $course
                ->setName(sprintf('example%d', $i))
                ->setDescription(sprintf('example%d', $i))
            ;

            $em->persist($course);
            $em->flush();
        }
    }

    protected function insertUsers(InputInterface $input, OutputInterface $output) {
        if ($lastUser = $this->doctrine->getRepository('AppBundle:Person')->findOneBy([], ['id' => 'DESC'])) {
            $startId = $lastUser->getId();
        } else {
            $startId = 0;
        }
        $em = $this->doctrine->getManager();
        $maxuserspercourse = $input->getArgument('maxuserspercourse');
        $usercount = $startId + $input->getArgument('usercount');

        $courselist = $this->doctrine->getRepository('AppBundle:Course')->findAll();

        for ($i = $startId; $i < $usercount; $i++) {
            $person = new Person();
            $person
                ->setUsername(sprintf('exampleuser%d', $i))
                ->setFirstname(sprintf('exampleuser%d', $i))
                ->setLastname(sprintf('exampleuser%d', $i))
                ->setPassword('password')
                ->setEmail(sprintf('exampleuser%d@example.invalid', $i))
            ;

            $courseIds = array_rand($courselist, rand(1, $maxuserspercourse));
            if (!is_array($courseIds)) {
                $courseIds = [$courseIds];
            }
            foreach ($courseIds as $courseId) {
                $course = $courselist[$courseId];
                $person->addCourse($course);
            }

            $output->writeln(sprintf(
                '<info>Added user %s with %d enrolments</info>',
                $person->getUsername(),
                count($courseIds)
            ));

            $em->persist($person);
            $em->flush();
        }
    }
}

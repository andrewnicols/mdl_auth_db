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

class MoodleConfigCommand extends ContainerAwareCommand {
    protected function configure() {
        $this
            ->setName('appbundle:moodleconfig')
            ->setDescription('Moodle Config Dump')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getApplication()->getKernel()->getContainer();

        $dbconfig = [
            'host'          => $container->getParameter("database_host"),
            'sybasequoting' => '0',
            'name'          => $container->getParameter('database_name'),
            'user'          => $container->getParameter('database_user'),
            'pass'          => $container->getParameter('database_password'),
            'table'         => 'person',
            'fielduser'     => 'username',
            'fieldpass'     => 'password',
            'passtype'      => 'plaintext',
        ];

        $fields = [
            'firstname'         => 'firstname',
            'lastname'          => 'lastname',
            'email'             => 'email',
            'idnumber'          => 'id',
        ];
        foreach ($fields as $name => $map) {
            $dbconfig['field_map_' . $name] = $map;
            $dbconfig['field_updatelocal_' . $name] = 'onlogin';
            $dbconfig['field_updateremote_' . $name] = 0;
            $dbconfig['field_lock_' . $name] = 'locked';
        }


        $enrolconfig = [
 			'dbhost'                  => $container->getParameter("database_host"),
 			'dbencoding'              => 'utf-8',
 			'dbsybasequoting'         => '0',
 			'debugdb'                 => '0',
 			'localcoursefield'        => 'idnumber',
 			'localuserfield'          => 'idnumber',
 			'localrolefield'          => 'shortname',
 			'localcategoryfield'      => 'id',
 			'defaultrole'             => '5',
 			'ignorehiddencourses'     => '0',
 			'unenrolaction'           => '0',
 			'defaultcategory'         => '1',
            'dbname'        		  => $container->getParameter('database_name'),
            'dbuser'        		  => $container->getParameter('database_user'),
            'dbpass'        		  => $container->getParameter('database_password'),
 			'remotecoursefield'       => 'course_id',
 			'remoteuserfield'         => 'person_id',
 			'newcoursefullname'       => 'name',
 			'newcourseshortname'      => 'name',
 			'newcourseidnumber'       => 'id',
 			'remoteenroltable'        => 'person_course',
 			'newcoursetable'          => 'course',
        ];

        if ($container->getParameter('database_driver') === 'pdo_pgsql') {
 			$enrolconfig['dbtype'] = 'postgres';
 			$dbconfig['type'] = 'postgres';
        } else {
 			$enrolconfig['dbtype'] = 'mysqli';
 			$dbconfig['type'] = 'mysqli';
        }

        echo "\$CFG->auth = 'email,manual,db';\n";
        echo "\$CFG->enrol_plugins_enabled = 'manual,guest,self,cohort,database';\n";
        echo "if (!isset(\$CFG->forced_plugin_settings)) {\n    \$CFG->forced_plugin_settings = [];\n}\n";
        echo '$CFG->forced_plugin_settings["auth/db"] = '           . var_export($dbconfig, true) . ";\n";
        echo '$CFG->forced_plugin_settings["enrol_database"] = '    . var_export($enrolconfig, true) . ";\n";
    }
}

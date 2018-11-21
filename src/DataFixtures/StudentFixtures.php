<?php

namespace App\DataFixtures;

use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StudentFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0 ; $i < 10; $i++)
        {
            $student = new Student();
            $student->setName('Lilian Gautier' . $i);
            $student->setAge('18');
            $student->setDescription('Jsuis beau');
            $student->setClass(Student::CLASSES[0]);

            $manager->persist($student);
        }

        $manager->flush();
    }
}

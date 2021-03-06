<?php


namespace app\controllers;


use app\models\school;
use app\models\classroom;
use app\models\student;
use app\models\enrollment;
use yii\db\Query;
use Yii;

class LoadController extends \yii\web\Controller {
    /**
     * Get Enrollments by school from TAG
     * 
     * @param string $inep
     * @return array
     */
    private function getSchoolTAG($inep){
        /** HB              TAG
         * school       to  school_identification
         * id               inep_id
         * fid              inep_id
         * name             name
         * phone            phone_number
         * address          1
         * principal        null
         */
        $query = "select inep_id as id, inep_id as fid, name, phone_number as phone, 1 as address "
                . "from school_identification "
                . "where inep_id = ".$inep;
        $result = Yii::$app->tag->createCommand($query);
        return $result->queryAll();
        
    }
    
    /**
     * Get classrons by school from TAG
     * 
     * @param string $school
     * @return array
     */
    private function getClassroomsTAG($school,$year){
        /** HB              TAG
         * classroom  to    classroom
         * id               id
         * fid              id
         * school           school_inep_fk
         * name             name
         * shift            turn 
         * year             school_year
         */
        $query = "select 'null' as id, id as fid, `name`, school_inep_fk as school,
		if(turn = 'M', 'morning',if(turn = 'T', 'afternoon',if(turn = 'N', 'night','null' ))) as shift,
		school_year as `year` "
                ."from classroom "
                ."where school_inep_fk = ".$school." "
                ."and school_year = ".$year;
        $result = Yii::$app->tag->createCommand($query);
        return $result->queryAll();
    }
    
    /**
     * Get Students by school from TAG
     * 
     * @param string $school
     * @return array
     */
    private function getStudentsTAG($id){
        /** HB              TAG
         * student  to  student_identification
         * id           id
         * fid          id
         * name         name
         * address      null
         * birthday     birthday
         * gender       gender if(1, 'male', 'female')
         * mother       mother_name
         * father       father_name
         * 
         */
        $query = "select 'null' as id, id as fid, `name`, 'null' as address, birthday, 
		if(sex = 1, 'male', 'female') as gender, 
		mother_name as mother, father_name as father "
                ."from student_identification "
                ."where id = ".$id;
        $result = Yii::$app->tag->createCommand($query);
        return $result->queryOne();
    }

    /**
     * Get Enrollments by school from TAG
     * 
     * @param string $school
     * @return array
     */
    private function getEnrollmentsTAG($classroom){
        /** HB              TAG
         * enrollment  to   student_enrollment
         * id               null
         * student          student_fk
         * classroom        classroom_fk
         */
        $query = "select 'null' as id, student_fk as student, classroom_fk as classroom "
                ."from student_enrollment "
                ."where classroom_fk = ".$classroom;
        $result = Yii::$app->tag->createCommand($query);
        return $result->queryAll();
    }


    public function actionTag() {
        set_time_limit(0);
        
        $schools = $this->getSchoolTAG('28022122');
        $classrooms = $this->getClassroomsTAG('28022122', '2014');
        
        $i = $j = $k = $l = 0;
        foreach ($schools as $school){
            $newSchool = school::find()->where('id = :id', ['id' => $school['id']])->one();
            if(!isset($newSchool)){
                $newSchool = new school();
            }
            $newSchool->id = $school['id'];
            $newSchool->fid = $school['fid'];
            $newSchool->name = $school['name'];
            $newSchool->phone = $school['phone'];
            $newSchool->address = 1;
            $newSchool->save();
           
            echo "School[".$i++."]: ".$newSchool->name . " saved<br>";
        }
        foreach ($classrooms as $classroom){
            $newClassroom = new classroom();
            $newClassroom->id = $classroom['id'];
            $newClassroom->fid = $classroom['fid'];
            $newClassroom->school = $classroom['school'];
            $newClassroom->name = $classroom['name'];
            $newClassroom->shift = $classroom['shift'];
            $newClassroom->year = $classroom['year'];
            $newClassroom->save();
            
            echo "Classroom[".$j++."]: ".$newClassroom->name . " saved<br>";
            
            $enrollments = $this->getEnrollmentsTAG($newClassroom->fid);
            foreach ($enrollments as $enrollment){
                $student = $this->getStudentsTAG($enrollment['student']);
                
                $newStudent = new student();
                $newStudent->id = $student['id'];
                $newStudent->fid = $student['fid'];
                $newStudent->name = $student['name'];
                $newStudent->address = null;
                $newStudent->birthday = $student['birthday'];
                $newStudent->gender = $student['gender'];
                $newStudent->mother = $student['mother'];
                $newStudent->father = $student['father'];
                $newStudent->save();
                
                echo "Student[".$k++."]: ".$newStudent->name . " saved<br>";
                
                $newEnrollment = new enrollment();
                $newEnrollment->student = $newStudent->id;
                $newEnrollment->classroom = $newClassroom->id;
                $newEnrollment->save();
                
                echo "Enrollment[".$l++."]: ".$newEnrollment->id . " saved<br>";
            }
        }
        set_time_limit(30);
    }

}

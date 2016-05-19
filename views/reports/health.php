<?php
$this->title = yii::t('app', 'Health Report');
?>

<table class="health-report-table table table-bordered table-striped" style="width:100%">
    <tr>
        <th> ESCOLA</th>
        <th>ALUNOS MATRICULADOS</th>
        <th>ANATOMIA</th>
        <th>ALUNOS PARTICIPANTES</th>
        <th>HB1</th>
        <th>ANÊMICO HB1</th>
        <th>CONSULTA</th>
        <th>HB2</th>
        <th>ANÊMICO HB2</th>
        <th>HB3</th>
        <th>ALUNOS ANÊMICOS</th>
    </tr>
    <?php
    $campaing = new \app\models\campaign();
    $campaings = $campaing->find()->orderBy('name asc')->all();
    foreach ($campaings as $c) {

        $campaing = [];
        $campaing['Name'] = $c->name;

        $terms['Total'] = $c->getTerms()->count();
        $terms['Agreed'] = $c->getTerms()->where(["agreed" => true])->count();

        $anatomies['Updated'] = $c->getStudentsAnatomies()->where('anatomy.date >= :date', ['date' => $c->begin])->count();

        $hb1['Total'] = $terms['Agreed'];

        $hbAnemics['Total'] = [];

        $hbAnemics['Total'][1] = 0;

        $consults['Attended'] = $c->getConsults()->where(["attended" => '1'])->count();

        $hb2['Done'] = $c->getHemoglobins()->where(["sample" => '2'])->groupBy("agreed_term")->count();

        $hbAnemics['Total'][2] = 0;

        $hb3['Done'] = $c->getHemoglobins()->where(["sample" => '3'])->groupBy("agreed_term")->count();

        $hbAnemics['Total'][3] = 0;

        $t = \app\models\term::find()->where("campaign = :c1 AND agreed = 1", ["c1" => $c->id])
            ->innerJoin('enrollment as en', 'enrollment = en.id')
            ->innerJoin('student as s', 's.id = en.student')
            ->innerJoin('classroom as c', 'c.id = en.classroom')
            ->orderBy('c.name ASC, s.name ASC')
            ->all();
        for ($i = 1; $i < 4; $i++) {
            foreach ($t AS $termAgreed):
                // Somente possui uma hemoglobin por termo
                $hemoglobin = \app\models\hemoglobin::find()->where("agreed_term = :a AND sample = :s", ["a" => $termAgreed->id, "s" => $i])->one();

                if (isset($hemoglobin)) {
                    //Então pesquisa o student atravez do $termAgreed

                    $enrollment = \app\models\enrollment::find()->where("id = :a", ["a" => $termAgreed->enrollment])->one();
                    $student = \app\models\student::find()->where("id = :a", ["a" => $enrollment->student])->one();
                    $classroom = \app\models\classroom::find()->where("id = :a", ["a" => $enrollment->classroom])->one();
                    $school = \app\models\school::find()->where("id = :a", ["a" => $classroom->school])->one();

                    $rate = $hemoglobin->rate;
                    $nameStudent = $student->name;
                    $genderStudent = $student->gender;
                    $ageStudent = (strtotime($c->end) - strtotime($student->birthday)) / (60 * 60 * 24 * 30);


                    $isAnemic = false;
                    if (($ageStudent > 24) && ($ageStudent < 60)) {
                        $isAnemic = !($rate >= 11);
                    } else if (($ageStudent >= 60) && ($ageStudent < 144)) {
                        $isAnemic = !($rate >= 11.5);
                    } else if (($ageStudent >= 144) && ($ageStudent < 180)) {
                        $isAnemic = !($rate >= 12);
                    } else if ($ageStudent >= 180) {

                        if ($genderStudent == "male") {
                            $isAnemic = !($rate >= 13);
                        } else {
                            //female
                            $isAnemic = !($rate >= 12);
                        }
                    }

                    if ($isAnemic) {
                        $hbAnemics['Total'][$i]++;
                    }
                }

            endforeach;
        }
        ?>
        <tr>
            <td><?= $campaing['Name'] ?></td>
            <td><?= $terms['Total'] ?></td>
            <td><?= $anatomies['Updated'] ?></td>
            <td>
                <?= $terms['Agreed'] ?>
                <span class="health-report-percent">
                    (<?=
                    $terms['Total'] == 0 ? ($terms['Agreed'] == 0 ? 100 : 0) :
                        round((1 - ($terms['Total'] - $terms['Agreed']) / $terms['Total']) * 100, 2)
                    ?>%)
                </span>
            </td>
            <td>
                <?= $hb1['Total'] ?>
                <span class="health-report-percent">
                    (<?=
                    $hb1['Total'] == 0 ? ($terms['Agreed'] == 0 ? 100 : 0) :
                        round((1 - ($hb1['Total'] - $terms['Agreed']) / $hb1['Total']) * 100, 2)
                    ?>%)
                </span>
            </td>
            <td>
                <?= $hbAnemics['Total'][1] ?>
                <span class="health-report-percent">
                    (<?=
                    $hb1['Total'] == 0 ? ($hbAnemics['Total'][1] == 0 ? 100 : 0) :
                        round((1 - ($hb1['Total'] - $hbAnemics['Total'][1]) / $hb1['Total']) * 100, 2)
                    ?>%)
                </span>
            </td>
            <td>
                <?= $consults['Attended'] ?>
                <span class="health-report-percent">
                    (<?=
                    $hbAnemics['Total'][1] == 0 ? ($consults['Attended'] == 0 ? 100 : 0) :
                        round((1 - ($hbAnemics['Total'][1] - $consults['Attended']) / $hbAnemics['Total'][1]) * 100, 2)
                    ?>%)
                </span>
            </td>
            <td>
                <?= $hb2['Done'] ?>
                <span class="health-report-percent">
                    (<?=
                    $consults['Attended'] == 0 ? ($hb2['Done'] == 0 ? 100 : 0) :
                        round((1 - ($consults['Attended'] - $hb2['Done']) / $consults['Attended']) * 100, 2)
                    ?>%)
                </span>
            </td>
            <td>
                <?= $hbAnemics['Total'][2] ?>
                <span class="health-report-percent">
                    (<?=
                    $hb2['Done'] == 0 ? ($hbAnemics['Total'][2] == 0 ? 100 : 0) :
                        round((1 - ($hb2['Done'] - $hbAnemics['Total'][2]) / $hb2['Done']) * 100, 2)
                    ?>%)
                </span>
            </td>
            <td>
                <?= $hb3['Done'] ?>
                <span class="health-report-percent">
                    (<?=
                    $consults['Attended'] == 0 ? ($hb3['Done'] == 0 ? 100 : 0) :
                        round((1 - ($consults['Attended'] - $hb3['Done']) / $consults['Attended']) * 100, 2)
                    ?>%)
                </span>
            </td>
            <td>
                <?= $hbAnemics['Total'][3] ?>
                <span class="health-report-percent">
                    (<?=
                    $hbAnemics['Total'][1] == 0 ? ($hbAnemics['Total'][3] == 0 ? 100 : 0) :
                        round((1 - ($hbAnemics['Total'][1] - $hbAnemics['Total'][3]) / $hbAnemics['Total'][1]) * 100, 2)
                    ?>%)
                </span>
            </td>
        </tr>
    <?php } ?>
</table>

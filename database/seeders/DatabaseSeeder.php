<?php
namespace Database\Seeders;
use App\Models\{User,Course,CourseModule,Lesson,Quiz,Question,QuestionOption,Enrollment};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{Hash,DB};
use Spatie\Permission\Models\{Role,Permission};

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $adminRole       = Role::firstOrCreate(['name'=>'admin',       'guard_name'=>'web']);
        $instructorRole  = Role::firstOrCreate(['name'=>'instructor',   'guard_name'=>'web']);
        $participantRole = Role::firstOrCreate(['name'=>'participant',  'guard_name'=>'web']);

        foreach (['manage-users','manage-courses','view-reports','create-courses','edit-courses','grade-participants','view-courses','take-quizzes'] as $p)
            Permission::firstOrCreate(['name'=>$p,'guard_name'=>'web']);

        $adminRole->syncPermissions(Permission::all());
        $instructorRole->syncPermissions(['create-courses','edit-courses','grade-participants','view-courses','view-reports']);
        $participantRole->syncPermissions(['view-courses','take-quizzes']);

        $admin = User::firstOrCreate(['email'=>'admin@carder.gov.co'],['name'=>'Administrador CARDER','password'=>Hash::make('password'),'cargo'=>'Coordinador TI','dependencia'=>'Sistemas y Tecnologías','email_verified_at'=>now(),'active'=>true]);
        $admin->syncRoles(['admin']);

        $ins1 = User::firstOrCreate(['email'=>'instructor@carder.gov.co'],['name'=>'Juliana Ríos Peña','password'=>Hash::make('password'),'cargo'=>'Profesional Educación Ambiental','dependencia'=>'Dirección de Educación Ambiental','email_verified_at'=>now(),'active'=>true]);
        $ins1->syncRoles(['instructor']);

        $ins2 = User::firstOrCreate(['email'=>'instructor2@carder.gov.co'],['name'=>'Carlos Mejía Zuluaga','password'=>Hash::make('password'),'cargo'=>'Profesional Gestión del Riesgo','dependencia'=>'Dirección de Gestión del Riesgo','email_verified_at'=>now(),'active'=>true]);
        $ins2->syncRoles(['instructor']);

        $pData = [['Ana Milena Torres','ana.torres@carder.gov.co','Técnica Administrativa','Dirección Administrativa'],['Luis Fernando Cano','luis.cano@carder.gov.co','Profesional Hídrico','Gestión del Recurso Hídrico'],['Paola Andrea Giraldo','paola.giraldo@carder.gov.co','Inspectora Ambiental','Control Ambiental'],['Diego Salazar Rivera','diego.salazar@carder.gov.co','Prof. Biodiversidad','Biodiversidad y Ecosistemas'],['Participante CARDER','participante@carder.gov.co','Técnico Ambiental','Dirección General']];
        $participantes = [];
        foreach ($pData as [$name,$email,$cargo,$dep]) {
            $u = User::firstOrCreate(['email'=>$email],['name'=>$name,'password'=>Hash::make('password'),'cargo'=>$cargo,'dependencia'=>$dep,'email_verified_at'=>now(),'active'=>true]);
            $u->syncRoles(['participant']); $participantes[] = $u;
        }

        $c1 = Course::firstOrCreate(['title'=>'Gestión del Recurso Hídrico en Risaralda'],['instructor_id'=>$ins1->id,'description'=>'Capacitación sobre administración y conservación de los recursos hídricos del departamento. Abarca Ley 99/1993, Decreto 3930/2010, cuencas hidrográficas y monitoreo.','category'=>'hidrico','duration_hours'=>8,'status'=>'published']);
        $m1 = CourseModule::firstOrCreate(['course_id'=>$c1->id,'title'=>'Módulo 1: Fundamentos Hídricos'],['description'=>'Marco conceptual y normativo','order'=>1]);
        Lesson::firstOrCreate(['module_id'=>$m1->id,'title'=>'El ciclo hidrológico en Risaralda'],['content'=>'<h2>El Ciclo Hidrológico</h2><p>En <strong>Risaralda</strong> los principales afluentes son el <strong>río Otún</strong> y el <strong>río Cauca</strong>. La CARDER opera 14 estaciones hidrometeorológicas.</p><h3>Componentes</h3><ul><li><strong>Precipitación:</strong> 1.800-3.500 mm/año</li><li><strong>Evapotranspiración:</strong> Devuelve agua a la atmósfera</li><li><strong>Escorrentía:</strong> Flujo hacia ríos y quebradas</li><li><strong>Infiltración:</strong> Recarga de acuíferos</li></ul>','type'=>'text','order'=>1]);
        Lesson::firstOrCreate(['module_id'=>$m1->id,'title'=>'Marco normativo: Ley 99 de 1993'],['content'=>'<h2>Marco Normativo</h2><p>La <strong>Ley 99 de 1993</strong> crea el Ministerio del Medio Ambiente y el SINA. El <strong>Decreto 3930 de 2010</strong> regula los usos del recurso hídrico. El <strong>Decreto 1076 de 2015</strong> compila toda la normatividad ambiental.</p>','type'=>'text','order'=>2]);
        $m2 = CourseModule::firstOrCreate(['course_id'=>$c1->id,'title'=>'Módulo 2: Calidad y Monitoreo'],['description'=>'Parámetros de calidad del agua','order'=>2]);
        Lesson::firstOrCreate(['module_id'=>$m2->id,'title'=>'Parámetros de calidad del agua'],['content'=>'<h2>Parámetros de Calidad</h2><ul><li><strong>pH:</strong> 6.5-8.5 para agua potable</li><li><strong>DBO5:</strong> Demanda Biológica de Oxígeno</li><li><strong>Oxígeno Disuelto:</strong> mayor a 4 mg/L</li><li><strong>Coliformes fecales:</strong> contaminación biológica</li><li><strong>SST:</strong> Sólidos Suspendidos Totales</li></ul>','type'=>'text','order'=>1]);
        $q1 = Quiz::firstOrCreate(['course_id'=>$c1->id,'title'=>'Evaluación: Recurso Hídrico'],['description'=>'Evalúa tus conocimientos sobre gestión del recurso hídrico.','passing_score'=>70,'max_attempts'=>3]);
        $this->preguntas($q1,[['¿Qué ley crea el Sistema Nacional Ambiental (SINA)?',['Ley 66 de 1981','Ley 99 de 1993','Ley 142 de 1994','Ley 388 de 1997'],1],['¿Cuántos municipios conforman la jurisdicción de la CARDER?',['Diez','Doce','Catorce','Dieciséis'],2],['¿Cuál es el principal río de abastecimiento de Pereira?',['Río Cauca','Río Otún','Río Consota','Río Barbas'],1],['¿Qué parámetro indica materia orgánica en el agua?',['pH','Turbidez','DBO5','Temperatura'],2],['¿Qué decreto regula los usos del recurso hídrico?',['Decreto 1600/1994','Decreto 3930/2010','Decreto 1076/2015','Decreto 2811/1974'],1]]);

        $c2 = Course::firstOrCreate(['title'=>'Educación Ambiental y Participación Ciudadana'],['instructor_id'=>$ins1->id,'description'=>'Fortalecimiento de capacidades en educación ambiental. Incluye PRAE, PROCEDA y Ley 2384 de 2024.','category'=>'educacion_ambiental','duration_hours'=>6,'status'=>'published']);
        $m3 = CourseModule::firstOrCreate(['course_id'=>$c2->id,'title'=>'Módulo 1: Instrumentos de EA'],['description'=>'PRAE y PROCEDA','order'=>1]);
        Lesson::firstOrCreate(['module_id'=>$m3->id,'title'=>'¿Qué son los PRAE?'],['content'=>'<h2>Proyectos Ambientales Escolares (PRAE)</h2><p>Proyectos pedagógicos <strong>obligatorios</strong> (Decreto 1743 de 1994) que incorporan la problemática ambiental local al quehacer educativo.</p>','type'=>'text','order'=>1]);
        Lesson::firstOrCreate(['module_id'=>$m3->id,'title'=>'Ley 2384 de 2024'],['content'=>'<h2>Ley 2384 de 2024</h2><p>Establece la Política Nacional de Educación Ambiental y crea mecanismos de financiación para las corporaciones autónomas regionales.</p>','type'=>'text','order'=>2]);
        $q2 = Quiz::firstOrCreate(['course_id'=>$c2->id,'title'=>'Evaluación: Educación Ambiental'],['description'=>'Evalúa tus conocimientos sobre educación ambiental.','passing_score'=>70,'max_attempts'=>3]);
        $this->preguntas($q2,[['¿Qué decreto hace obligatorios los PRAE?',['Decreto 1076/2015','Decreto 1743/1994','Decreto 3930/2010','Decreto 948/1995'],1],['¿Qué significa PROCEDA?',['Proyecto Ciudadano de Educación Ambiental','Programa de Control Ambiental','Proyecto Comunitario de Desarrollo','Protocolo de Conservación'],0],['¿Qué ley establece la Política Nacional de EA (2024)?',['Ley 1549/2012','Ley 99/1993','Ley 2384/2024','Ley 1931/2018'],2]]);

        $c3 = Course::firstOrCreate(['title'=>'Gestión del Riesgo y Cambio Climático'],['instructor_id'=>$ins2->id,'description'=>'Formación en identificación de amenazas, vulnerabilidad y estrategias de adaptación al cambio climático en Risaralda.','category'=>'gestion_riesgo','duration_hours'=>10,'status'=>'published']);
        $m4 = CourseModule::firstOrCreate(['course_id'=>$c3->id,'title'=>'Módulo 1: Conceptos del Riesgo'],['description'=>'Amenaza, vulnerabilidad y riesgo','order'=>1]);
        Lesson::firstOrCreate(['module_id'=>$m4->id,'title'=>'Triángulo del riesgo'],['content'=>'<h2>Riesgo = Amenaza x Vulnerabilidad</h2><p>La CARDER identifica: movimientos en masa, inundaciones, avenidas torrenciales, sismos e incendios forestales en Risaralda.</p>','type'=>'text','order'=>1]);
        Lesson::firstOrCreate(['module_id'=>$m4->id,'title'=>'Ley 1523 de 2012'],['content'=>'<h2>Ley 1523 de 2012</h2><p>Adopta la Política Nacional de Gestión del Riesgo y establece el SNGRD. La CARDER elabora estudios de amenaza y apoya a los 14 municipios.</p>','type'=>'text','order'=>2]);
        $q3 = Quiz::firstOrCreate(['course_id'=>$c3->id,'title'=>'Evaluación: Gestión del Riesgo'],['description'=>'Evalúa tus conocimientos sobre riesgo y cambio climático.','passing_score'=>70,'max_attempts'=>3]);
        $this->preguntas($q3,[['El riesgo es resultado de la interacción entre:',['Amenaza y territorio','Amenaza y vulnerabilidad','Desastre y emergencia','Comunidad y Estado'],1],['¿Qué ley adopta la Política Nacional de Gestión del Riesgo?',['Ley 99/1993','Ley 388/1997','Ley 1523/2012','Ley 1625/2013'],2],['¿Cuál es un ejemplo de amenaza natural?',['Contaminación hídrica','Deforestación','Movimiento en masa','Incendio industrial'],2]]);

        foreach ($participantes as $p) {
            foreach ([$c1,$c2] as $curso) {
                DB::table('enrollments')->insertOrIgnore(['user_id'=>$p->id,'course_id'=>$curso->id,'status'=>'active','enrolled_at'=>now()->subDays(rand(5,30)),'created_at'=>now(),'updated_at'=>now()]);
            }
        }
        $this->command->info('✅ Seeder completado: 3 cursos, 5 participantes, 2 instructores, 1 admin');
    }
    private function preguntas(Quiz $quiz, array $preguntas): void {
        foreach ($preguntas as $i => [$texto,$opciones,$correcta]) {
            $q = Question::firstOrCreate(['quiz_id'=>$quiz->id,'question_text'=>$texto],['order'=>$i+1]);
            foreach ($opciones as $j => $opt) QuestionOption::firstOrCreate(['question_id'=>$q->id,'option_text'=>$opt],['is_correct'=>$j===$correcta]);
        }
    }
}

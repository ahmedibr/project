<?php
// On charge le framework Silex
require_once 'vendor/autoload.php';

// On définit des noms utiles
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// On crée l'application et on la configure en mode debug
$app = new Application();
$app['debug'] = true;
$app->register(new Silex\Provider\DoctrineServiceProvider(),
  array('db.options' => array(
        'driver'   => 'pdo_mysql',
        'host'     => getenv('IP'),  // pas touche à ça : spécifique pour C9 !
        'user'     => substr(getenv('C9_USER'), 0, 16),  // laissez comme ça, ou mettez
                                                         // votre login à la place
        'password' => '',
        'dbname' => 'c9'  // mettez ici le nom de la base de données
  )));
  $app->register(new Silex\Provider\TwigServiceProvider(), 
               array('twig.path' => 'templates'));
 
	$app->register(new Silex\Provider\SessionServiceProvider());
	
	
	
$app->get('/',function(Application $app,Request $req){
     $createur=$app['session']->get('login');
    return $app['twig']->render('essai.html',array('login'=>$createur));
});
	

 $app->get('/signin', function(Application $app, Request $req) 
   {
         
       if( ($req->query->get("login")) && ($req->query->get("motdepasse")) )
       {
           
             $a=$req->query->get("login");
             $b=$req->query->get("motdepasse");
                                //users
         
                     $num = $app['db']->executeQuery('SELECT * FROM users where login=?',array($a));
         
                     if ($num->rowCount()!=0)
                             {   $recup=$num->fetchAll();
                                  foreach ($recup as $row)
                                     {
                                     $mdph=$row['pass'];
                                     }
                                 if (password_verify($b, $mdph))
                                   {
                                        $app['session']->set('login', $a);
                                         return $app->redirect('/connexion.php/');    //redirection vers la page principale
     
                                  }else return $app->redirect('/login.html');
                   
                             }else return $app->redirect('/login.html');
            } else return $app->redirect('/login.html');
           
      
  }); 
  
   $app->get('/signin_admin', function(Application $app, Request $req) 
   {
         
       if( ($req->query->get("login")) && ($req->query->get("motdepasse")) )
       {
           
             $a=$req->query->get("login");
             //echo ($a);
             $b=$req->query->get("motdepasse");
                               
                               $admin= $app['db']->executeQuery('SELECT * FROM administrateur where login=? AND pass=?',array($a,$b));
                  if ($admin->rowCount()!=0)
                     {
                             $app['session']->set('login', $admin);
                             
                                return $app->redirect('/connexion.php/'); 
                      }  //redirection vers la page principale
                      
                    else    return $app->redirect('/login.html');
                      
       }
       else return $app->redirect('/login.html');
   });
   
   
 $app->get('/deconnexion', function(Application $app, Request $req)
 { $app['session']->clear();
 return $app->redirect('/connexion.php/');
 });
 
 
 
 
  
  
 $app->post('/inscrire', function(Application $app, Request $req) 
   {   if( ($req->request->get("login")) && ($req->request->get("motdepasse")) )
       {
          $a=$req->request->get("login");
          $b=$req->request->get("motdepasse");
         $hash = password_hash($b, PASSWORD_BCRYPT);
         
          $num = $app['db']->executeQuery('SELECT * FROM users where login=? ',array($a));
         if ($num->rowCount()!=0)
           { 
              
            
           return ('<br><b>Login deja existant,veuillez saisir un autre</b>');  
               
           }
         else{ 
       
            $c = $app['db']->executeUpdate('INSERT INTO users VALUES (?,?)',array($a,$hash));
                                
            return $app->redirect('/login.html'); 
           }
       }
       else   return $app->redirect('/inscrire.html');  
               
        
       
   });
 
 
 //gestion des evenement
 $app->post('/evenement', function(Application $app, Request $req) 
    
    {
       $titre=$req->request->get("titre");
       $app['session']->set('titre',$titre);
    
       $date=$req->request->get("date");
       $app['session']->set('date',$date);
       $heure=$req->request->get("heure");
       
       $app['session']->set('heure',$heure);
        //****
       $createur=$app['session']->get('login');
   
    
    $button=$req->request->get("button");
    if($button=="ajouter")
    {    if($titre!=null )
        {
            $verif=$app['db']->executeQuery('SELECT * FROM evenement where date=? and heure=?',array($date,$heure));
         
            if ($verif->rowCount()==0)
             {
             $num = $app['db']->executeUpdate('INSERT INTO evenement VALUES (?,?,?,?)',array($titre,$date,$heure,$createur));
             return $app->redirect('/connexion.php/');
             }
             else return ("<br><b>Un autre evenement a été prévu dans cette date, choisissez une autre date!<b>");
    
       }
        else return ('<br><b>Le titre de l\'evenement doit etre saisi<b>');
    }
    else
        {
            if($button=="supprimer")
            {
                
                $verif=$app['db']->executeQuery('SELECT date,heure,createur FROM evenement where date=? AND heure=?  ',array($date,$heure));
                
                if($verif->rowCount()!=0)
                   { 
                       $num=$app['db']->executeQuery('SELECT date,heure,createur FROM evenement where date=? AND heure=? AND createur=? ',array($date,$heure,$createur));
                
                        if($num->rowCount()!=0)
                          {
                           $num=$app['db']->executeUpdate('DELETE FROM evenement where date=? AND heure=?',array($date,$heure));
                           return ("L'evenemet a été bien supprimé");
                              
                           }
                        else return ("Vous n'avez pas le droit de supprimer les evenements des autres utilisateurs");
                }
                 else return ("Il y a aucun evénement a supprimer");
            
            }else
               if($button=="modifier")
                   { 
                       $num = $app['db']->executeQuery('SELECT * FROM evenement where  date=? AND heure=?',array(
                           $app['session']->get('date'),$app['session']->get('heure')));
                        if ($num->rowCount()==0){
                            return ("Il y a aucun evénement à supprimer");
                        }else{
                $num = $app['db']->executeQuery('SELECT * FROM evenement where createur=? AND date=? AND heure=?',array($createur,$app['session']->get('date'),$app['session']->get('heure')));
                if ($num->rowCount()==0)
                return ("vous n'avez pas le droit de modifier les evenemests des autres utilisateurs");
                else
                return $app->redirect('/modifier.html');
               
                  }}
            
           
            
    
 }} );
 $app->post('/modifier', function(Application $app, Request $req) 
   {                      
           $c=$req->request->get("heure");
          
            $a=$req->request->get("titre");
              $b=$req->request->get("date");
            
              $num = $app['db']->executeQuery('SELECT * FROM evenement where titre=? AND date=? AND heure=? AND createur=?',array($a,$b,$c,$app['session']->get('login')));
               if($num->rowCount()!=0)
                {
                             return ('il existe deja un evenement a cette date et heure,veuillez choisir une autre date ou une autre heure ');
                }else{
                   
                      $u= $app['db']->executeUpdate('DELETE FROM evenement where titre=? and date=? and heure=? and createur=?' ,array($app['session']->get('titre'),$app['session']->get('date'),$app['session']->get('heure'),$app['session']->get('login')));
                      
                      $z= $app['db']->executeUpdate('INSERT INTO evenement VALUES (?,?,?,?)' ,array($a,$b,$c,$app['session']->get('login')));
                              return ('votre evenement a été bien modifier');
                     }
             
           
} );
 
 $app->post("/recup_event",function(Application $app,Request $req)
 {      
         $num = $app['db']->executeQuery('SELECT * FROM evenement')->fetchAll();
     return $app->json($num);
     
     
 });
 

 
 $app->run();

?>

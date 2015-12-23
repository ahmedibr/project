var T=[];
setInterval(recup_event,500);

     
window.onload = function()
{
//$=document.querySelector().bind(document);
 var listanne =['Janvier','Fevrier','Mars','Avril','May','Juin','Juillet','Aout','Semptembre','Octobre','Novemebre','decembre'];
  var d = new Date();
  var jour = d.getDay() ;//numero du jour lundi=1
  var date = d.getDate() ;//date du jour 20 mois
   var mois = d.getMonth() ; //4
   var annee = d.getFullYear(); 
   
   
  
   var d1=d;
    var d2=d;


$(function(e)
{ afficher_calendier();
   $('#calendarPrev').bind('click',function()
   {
       d2=jour_avant(getMonday(d1));
       d1=d2;
       afficher_calendier(d2); });
     $('#calendarNext').bind('click',function()
        { d2=semain_suivant(getMonday(d1));d1=d2;
           afficher_calendier(d2) ;});
});



function getMonday(d) {
  d = new Date(d);
  var day = d.getDay(),
      diff = d.getDate() - day + (day == 0 ? -6:1); // adjuster quand le jour est ddimanche ==0
  return new Date(d.setDate(diff));
 }
 function jour_avant(d)
   {d.setTime(d.getTime() - 24 * 3600 * 1000);//ajoute 24heure en ms
    return d;}
 
 function jour_suivant(d) {
    d.setTime(d.getTime() + 24 * 3600 * 1000);
   return d;}
 function semain_suivant(d) {
    d.setTime(d.getTime() + (24 * 3600 * 1000)*7);
   return d;}


function afficher_calendier (day)
{
 
 var tab_date=[];//sauvegarde les dates de la semaine courante
 var da;
 
    if(!day )
        da=new Date();
   else 
        da=new Date(day);
   
   
var premier_lundi=getMonday(da);
var jour_suiv=jour_suivant(premier_lundi);
var jour_s=jour_suiv;

 var hh="-1",mm="00"; 
   
   
  
 $('#calendarMonthYear').text(listanne[da.getMonth()] +" "+ da.getFullYear());//associé du texte(le mois et l'année) au div ayant pour identifiant #calendarMonthYear 
 $('#calendarWeekDaysBar').html('<br class="clear">');//sert a remplacer le contenu existant par un autre
 
 
 var tabjour=['','Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
 var TAB = document.createElement('table');
 TAB.id="calendrier";
    
    for(var i=0;i<=7;i++)
    {
       if(i==0){
           $('#calendarWeekDaysBar').append('<div class ="calendarWeekDay" >  </div>');//tab_date[i]='';
     }
      else if(i==1) 
        {
          $('#calendarWeekDaysBar').append('<div class ="calendarWeekDay" > '+ tabjour[i]+' '+getMonday(da).getDate()+"/ "+Number(getMonday(da).getMonth()+1)+'</div>'); 
          tab_date[i]=getMonday(da).getDate()+'-'+Number(getMonday(da).getMonth()+1)+'-'+getMonday(da).getFullYear();
        }
         else if(i==2) 
           {
            $('#calendarWeekDaysBar').append('<div class ="calendarWeekDay" > '+ tabjour[i]+' '+jour_suiv.getDate()+"/ "+Number(jour_suiv.getMonth()+1)+'</div>'); 
            tab_date[i]=jour_suiv.getDate()+'-'+Number(jour_suiv.getMonth()+1)+'-'+jour_suiv.getFullYear();
          
          }
     else { 
           jour_s=jour_suivant(jour_s);
           $('#calendarWeekDaysBar').append('<div class ="calendarWeekDay" > '+ tabjour[i]+' '+jour_s.getDate()+"/ "+Number(jour_s.getMonth()+1)+'</div>'); 
           tab_date[i]=jour_s.getDate()+'-'+Number(jour_s.getMonth()+1)+'-'+jour_s.getFullYear(); } 
     
          }
    
    
   
    for (var i = 0; i <= 47; i++) 
    {                                                      
        var l = document.createElement('tr');
         T[i] = [];
         for (var j = 0; j <=7; j++) 
         {
             
            T[i][j] = document.createElement('td');
            
          if( j!=0) {
                     T[i][j].dataset['date']=tab_date[j];
                    
          }
          if(j==0)
            {
              if(i%2==0 ) 
               { mm="00";hh++; }
              else
               mm="30"; 
              
             T[i][0].innerHTML=hh +':'+ mm ;
            }
             if(j!=0) 
             {
                 T[i][j].dataset['hour'] = hh +':'+ mm;   //association d'identifiant a chaque ligne cet id est l'heure
                 TAB.appendChild(l);
            
             }else{
              TAB.appendChild(l);}
           l.appendChild(T[i][j]);
       
         }
    }        
    
    
    $('#calendarBody').html('<br class="clear">');
    //var calendrier=get_calendrier();
    $('#calendarBody').append(TAB);
    
    
   if(login!="")
   {
    var btn = document.querySelector('#calendrier');
     btn.addEventListener('click',action);
     
    }else{ 
     var btn = document.querySelector('#calendrier');
     btn.addEventListener('click',v);
    }
    
   }

function v(){
  
   alert('Vous devez vous identifier pour interagir avec le calendrier');
}

    function action(e) 
    {
         var date=e.target.dataset['date'];
    
         if(date!=null)
         {                  //quand on clique  sur une collone non identifier par une date ça fait rien
           update(date);
           var heure=e.target.dataset['hour'];  
     
           var ele = document.getElementById('date');
           ele.setAttribute('value', date);
           var el = document.getElementById('heure');
           el.setAttribute('value', heure);
  
         }
        
    }
     
      
      function update(){
     document.getElementById("calendarwindow").style.display= 'block'; }
      
}


function quit(){
   document.getElementById("calendarwindow").style.display= 'none'; 
 
}
  

  function recup_event()
  {
  var xhr = new XMLHttpRequest();
   xhr.open("POST","/connexion.php/recup_event");
     xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onload = function() 
  {
      var  resultat=JSON.parse(xhr.responseText);
    for (var i=0;i<=7;i++)
    {
      for (var j=1;j<=47;j++)
       {   for( var k=0; k< resultat.length; k++)
         {
          if ((resultat[k].date== T[j][i].dataset['date'] ) && (resultat[k].heure== T[j][i].dataset['hour']) )
          {T[j][i].innerHTML='createur:'+resultat[k].createur+'<br>'+resultat[k].titre;
          
        
          T[j][i].style.backgroundColor='red';
   
          }
         }  
       }
    }
   
  
  }
  xhr.send();
  
  
}
 





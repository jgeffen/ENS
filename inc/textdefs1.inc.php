<?php
include_once $FILE_PATH . "/inc/textdefs0.inc.php";
# Text definitions for Spanish/Espa�ol
$helptext = "";
#$helptext = "�Ayuda?";
$systemnametext = "USGS Sistema de Notificaci&#243;n para Temblores";
$welcometext = "Bienvenidos";
$backtoprofiletext = "Regreso a las selecciones";
$showonlyeventstext = "Solo ense&#241;e temblores mas grande de magnitud";
$showonlynettext = "Solo ense&#241;e temblores reportado por redes sismol&#243;gicas";
$filtereventstext = "Filtrar notificaci&#243;ns";
$welcomemessagetext = "<p>Bienvenido al Servicio de Notificaci&#243;n de Terremotos (SNT).  <strong>Dos perfiles de notificaci&#243;n</strong> han sido predefinidos para su uso. Los mismos pueden ser editados o borrados. Uno de los perfiles enviará (please check accent code) notificaciones para todos los eventos en los <strong>EU con magnitud 4.5 o mayor</strong>. El otro perfil enviará (please check accent code) notificaciones para todos los eventos <strong>en el Mundo con magnitud 5.5 o mayor.</strong>. Usted tambien puede crear otros perfiles de notificación específicos (please check accent code)utilizando los botones a la izquierda.</p>
<p>Note que si utiliza cualquier tipo de <strong>\"spam-blocker\"</strong> en su cuenta de correo electr&#243;nico, debe asegurarse de permitir que los mensajes del SNT entren a su cuenta.  Si la cuenta va a estar desatendida por un periodo de tiempo, configure su contestador autom&#233;tico para que no envie mensajes al SNT.  De no hacerlo, su cuenta de SNT podr&#237;a desactivarse o ser cancelada.</p>";

# Definitions for register.php
$regverificationtext = "Por favor escriba su<a href=\"http://plus.maths.org/issue23/news/captcha/\" target=\"_blank\">c&#243;digo de verificaci&#243;n </a> en el espacio provisto. No necesita usar letras may&#250;sculas.<br>";
 $editdaybegintext = "Hora de Comienzo";
 $editdayendstext = "Hora Final";
 $regemailtext = "Correo electr&#243;nico";

# Definitions for send_sample_email.inc.php
//$sampleemailtitletext = "Try out ENS";
$sampleemailentertext = "Escriba su correo electr&#243;nico";
$sampleemailsubmit = "Enviar";

# Definitions for mailparams.php

$mailparamsbreadcrumbstext = "Sistema de Notificaci&#243;n para Temblores / Perfiles de Notificaci&#243;n";
$profilesheadertext = "<h2 id=\"pageName\"> Los Perfiles de la Notificati&#243;nes de Temblores Asociados con la Cuenta de %s's </h2>";
$viewrecenttext = "Revise las notificaciones recientes recibidas en su cuenta";
$profilescount1text = "%d el perfil del correo encontrado para su cuenta:";
$profilescount2text = "%d los perfiles del correo encontraron para su cuenta:";
$noprofilestext = "Usted no ha definido un perfil - por favor cree un perfil usando uno de los 4 botones en la derecha.<br> Someta direcciones adicionales para las notificaciones de temblores seleccionando la \"Nueva Direcci&#243;n\".<br> Su informaci&#243;n puede ser actualizada o cancelada seleccionando \"Corrija su Cuenta\".";
 $noaddresstext = " Usted no tiene ninguna direcci&#243;n de email en la base de datos. Por favor registre una direcci&#243;n para recibir notificaciones de temblores <br>";

 $geoboundstext = "L&#237;mites Geogr&#225;ficos";
 $placenametext = "Nombre";
 $nametext = "Nombre";
 $daymagtext = " Magnitud durante el Dia";
 $nightmagtext = " Magnitud durante la Noche";
 $allmagtext = "Magnitud";
 $alltimestext = "Cualquier Hora";
 $networkstext = "Redes Sismol&#243;gicas";
 $addresstext = "Direcci&#243;n";

$assocemailtext = "Emails Asociados Con Su Cuenta ";
 $addnewprofiletext = "Agregue Un Nuevo Perfil";

$profileedittext = "Corrija Este Perfil";

# Definitions for mailparamsedit1.php

$edit1breadcrumbtext1 = "Sistema de Notificaci&#243;n para Temblores / Perfiles de Notificaci&#243;n / Corrija un perfil existente";
$edit1breadcrumbtext2 = "Sistema de Notificaci&#243;n para Temblores / Perfiles de Notificaci&#243;n / Agregue Un Nuevo %s Perfil";
 $edit1headertext = "Par&#225;metros del email para las notificaciones de temblores";
 $edit1predeftext = "Predefinido";
 $addresseslabeltext = "<b>Correo Electr&#243;nico:</b>";
 $activelabeltext = "<b>Activo:</b>";
 $noaddresseslabeltext = "Usted no tiene direcciones en la base de datos. No puedo crear un perfil sin una direcci&#243;n de correo eletr&#243;nico.";
 $editnetworkstext = "<b>Redes Sismol&#243;gicas</b>";
 $definerectangletext = " Defina Un L&#237;mite Rectangular";
 $definerectanglenlat = "Latitud Norte";
 $definerectangleslat = "Latitud Sur";
 $definerectangleelon = "Longitud Este";
 $definerectanglewlon = " Longitud Oeste";
 $definecircletext = "<b>Defina Un L&#237;mite Circular</b>";
 $definecircleclat = " Latitud:Central";
 $definecircleclon = "Longitud:Central";
 $definecircleradius = "Radio (millas)";
 $definecircleplacename = "Nombre Para Este Lugar ";
 $definecircleselectplace = "<b>O</b> seleccione un lugar";
 $definecirclepickpoints = "<b>O</b> escoja puntos en un mapa";
 $definepolygontext = "<b>Defina un l&#237;mite del pol&#237;gono</b>";
 $createnewpolytext = "<b>Cree un nuevo l&#237;mite del pol&#237;gono</b>";
 $pickpointstext = "Escoja nuevos puntos ";
 $pickcircletext = "Defina un c&#237;rculo en el mapa ";
 $uploadxmltext = "Env&#237;e un archivo de XML";
 $editcurrentpolytext = "<b>Opciones Avanzadas Para Corregir</b>";
 $polygoneditortext = "Editor Del Pol&#237;gono";
 $xmledittext = "Editar en XML";
 $latloninputtext = "Inserte valores de latitud/longitud";
 $cannedprofiletext = "Escoja Un Perfil Predefinido";
$cannedpreviewtext = "Preliminar";
$cannedcurrentselectedtext = "Selecci&#243;n Presente";
 $cannedselecttext = "Escoja Perfil";
 $editdaymagtext = "<b>Magnitud durante el D&#237;a:</b>";
 $editnightmagtext = "<b>Magnitud durante la Noche:</b>";
 $editdepthtext = "<b>Profundidad Del Terremotos (km):</b>";
 $editprofilenametext = "<b>Nombre para este perfil:</b>";
 $editsubmittext = "Someta La Informaci&#243;n";
$editdeleteprofiletext = "Eliminar este perfil";

# Definitions for mailparamsedit2.php

$edit2breadcrumbtext1 = "Sistema de Notificaci&#243;n Para Temblores / Perfiles de Notificaci&#243;n / Corrija Un Perfil Existente ";
$edit2breadcrumbtext2 = "Sistema de Notificaci&#243;n para Temblores / Perfiles de Notificaci&#243;n / Agregue Un Nuevo Perfil de %s ";
 $edit2headertext = "Par&#225;metros Para El Correo Electr&#243;nico de Informaci&#243;n de Temblores";

 $edit2predeftext = "Predefinido";
 $definecirclenoradius = "<br>Error! Usted no puede definir un c&#237;rculo con un radio de 0!";
 $edit2recordtext = "Expediente en la base de datos actualizado";
 $edit2inserttext = "Nuevo expediente creado en la base de datos";
 $edit2submittext = "Contin&#249;e";

# Definitions for mailparamsdelete.php

$profiledeletebreadcrumbtext = "Sistema de Notificaci&#243;n Para Temblores / Perfiles de Notificaci&#243;n / Cancele Un Perfil Del Correo Electr&#243;nico";
 $profiledeleteheadertext = "Cancele Un Perfil Del Correo Electr&#243;nico";
 $deletedaymagtext = "Magnitud durante el D&#237;a";
 $deletenightmagtext = "Magnitud durante la Noche";
 $deletedaybegintext = "Hora de Comienzo";
 $deletedayendstext = "Hora Final";
 $profiledeleteconfirmtext = "&#191;Cancelar el Perfil?";
 $profiledeletetext = "Perfil en la base de datos cancelado";
 $profiledeletecontinuetext = "Contin&#249;e";

# Definitions for emailedit.php

 $maildaybegintext = "<b>Hora de Comienzo:</b>";
 $emaildayendstext = "<b>Hora Final:</b>";
$emaileditbreadcrumbtext = "Sistema de Notificaci&#243;n Para Temblores / Perfiles de Notificaci&#243;n / Corrija Su Direcci&#243;n del Correo Electr&#243;nico";
 $emaileditheadertext = "<h2>Corrija Su Dirección de Correo Electr&#243;nico</h2>";
 $emaileditlabeltext = "Dirección de Correo Electr&#243;nico";
 $emaileditformattext = "Formato del Mensaje";
 $emaileditconfirmtext = "Someta la Informaci&#243;n";
 $emaildeletetext = "Cancele Esta Direcci&#243;n de Correo Electr&#243;nico";
 $emailedittext = "Archivo de base de datos para correo electr&#243;nico actualizado";
 $emaileditcontinuetext = "Contin&#249;e";

# Definitions for deleteaddress.php

$emaildelbreadcrumbtext = "Sistema de Notificaci&#243;n Para Temblores / Perfiles de Notificaci&#243;n / Cancele Una Direcci&#243;n de Correo Electr&#243;nico";
 $emaildelheadertext = "<h2>Cancele Una Direcci&#243;n de Correo Electr&#243;nico</h2>";
 $emaildelwarningtext = "Esto cancelar&#225; la direcci&#243;n de email<em>%s</em>. Tambi&#233;n cancelar&#225; cualquier perfil del email que tenga <em>%s</em> como su &#249;nica direcci&#243;n.";
 $emaildelquerytext = "&#191;Est&#225;s seguro que deseas cancelar la direcci&#243;n <em>%s</em>?";
 $emaildelconfirmtext = "Cancele Esta Direcci&#243;n";
 $emaildeltext = "Direcci&#243;n <em>%s</em> ha sido cancelada";
 $emaildelcontinuetext = "Contin&#249;e";

# Definitions for passwd.php

$passwdbreadcrumbtext = "Sistema de Notificaci&#243;n Para Temblores / Corrija Su Cuenta";
 $passwdheadertext = "<h2>Corrija Su Contrase&#241;a</h2>";
 $passwdnomatchtext = "Error: &#161;Las nuevas contrase&#241;as no son iguales! Por favor intente otra vez.";
 $passwdupdatetext = "Su cuenta ha sido actualizada";
 $passwdcontinuetext = "Contin&#249;e";
 $passwdemailtext = "Correo Electr&#243;nico";	 
 $passwdusernametext = "Nombre del usuario";
 $passwdnametext = "Su Nombre";
 $passwdaddresstext = "Direcci&#243;n";
 $passwdcitytext = "Ciudad";
 $passwdstatetext = "Estado";
 $passwdziptext = "C&#243;digo postal";
 $passwdphonetext = "N&#249;mero de tel&#233;fono";
 $passwdtimezonetext = "Zona de Horario";
 $whatsmytimezonetext = "<a href=\"http://aa.usno.navy.mil/faq/docs/world_tzones.html\" target=\"_blank\">&#191;Cu&#225;l es mi zona de horario?</a>";
 $passwdlanguagetext = "Idioma Preferido";
 #$passwdpasswdtext = "Contrase&#241;a";
 $passwdpasswdtext = "Contrase&#241;a nueva";
 #$passwdconfirmtext = "Confirmar";
 $passwdconfirmtext = "Confirme la Nueva Contrase&#241;a";
 $passwdlanguagetext = "Idioma Preferido";
 $passwduserclasstext = "Clase de Usuario";
 $passwdaftershocktext = "&#191;Exclusi&#243;n de R&#233;plicas?";
 $passwdupdatestext = "&#191;Recibir eventos actualizados?";
 $passwddefertext = "&#191;Atrasar notificaciones durante la noche?";
 $passwdaffiliationtext = "Su Afiliaci&#243;n";
 $passotherinteresttext = "S&iacute; otra, escriba aqu&iacute;";
 $passwdpasswdchangetitle = "Cambie la Contrase&#241;a";
 $passwdpasswdchangetext = "Deje este el espacio en blanco a menos que usted desee cambiar su contrase&#241;a";
 $passwdsubmittext = "Someta La Informaci&#243;n";
 $passwddeletetext = "Cancele Su CContrase&#241;a";

# Definitions for deleteaccount.php

$acctdelbreadcrumbtext = "Sistema de Notificaci&#243;n Para Temblores / Perfiles de Notificaci&#243;n / Cancele Su Cuenta";
 $acctdelheadertext = "Cancele Su Cuenta";
 $acctdelwarningtext = "Esto cancelar&#225; su cuenta ' %s' y todas las direcciones del email y perfiles de la notificaci&#243;n del correo asociados a la misma.";
 $acctdelquerytext = "<em>&#191;Est&#225; usted seguro que quiere hacer esta</em>?";
 $acctdelconfirmtext = "Cancele Esta Cuenta";
 $acctdelnoconfirmtext = "No, no cancele";
 $acctdeltext = "Su cuenta ha estado cancelada";
 $acctdelcontinuetext = "Contin&#249;e";

# Definitions for newaddressreg.php

$newaddrbreadcrumbtext = "Sistema de Notificaci&#243;n Para Temblores / Perfiles de Notificaci&#243;n / Registre una Nueva Direcci&#243;n";
 $newaddrheadertext = "<h2>Registre una Nueva Direcci&#243;n de Correo Electr&#243;nico</h2>";
 $newaddremailtext = "Correo Electr&#243;nico";
 $newaddrreplacetext = "Substituye";
 $newaddrtext = "Este es un direcci&#243;n  nueva";
 $newaddrformat0text = "Formato HTML";
 $newaddrformat1text = "Correo Electr&#243;nico Regular";
 $newaddrformat2text = "Beeper/Pager/Tel&#233;fono Celular";
 $newaddrformat3text = "Formato Crudo \"CUBE\"";
 $newaddrnotetext = "<font color=\"red\"><b>Un n&#249;mero de confirmaci&#243;n ser&#225; enviado a la direcci&#243;n que usted someti&#243;. Guarde por favor este n&#249;mero para entrar en la p&#225;gina siguiente. Puede haber un corto retraso para el pager/correo de tel&#233;fono celular.</b></font>";
 $newaddrsubmittext = "Someta La Informaci&#243;n";
 $newaddrconftext = "Para prop&#243;sitos de seguridad y para confirmar que usted escribi&#243; su direcci&#243;n correctamente, hemos enviado un mensaje a su correo electr&#243;nico con un n&#249;mero tridigital de confirmaci&#243;n. Incorpore este n&#249;mero en el espacio abajo.";
 $newaddremailsubjtext = "Confirmaci&#243;n de la Direcci&#243;n";
 $newaddremailconftext = "Su n&#249;mero de la confirmaci&#243;n es %03d";
 $newaddrnomatchtext = "&#161;Los n&#249;meros de la confirmaci&#243;n no son iguales!";
 $pendingtext = "Tiene direcci&#243;nes el esperar para confirmando";

# Definitions for recentevents.php

$recenteventsbreadcrumb1text = "Sistema de Notificaci&#243;n Para Temblores / Los �ltimos Temblores Tratados";
 $recenteventsbreadcrumb2text = "Sistema de Notificaci&#243;n Para Temblores / Perfiles de Notificaci&#243;n / Los �ltimos Temblores Tratados para Usuario User %s";
 $recenteventsheadertext = "Terremotos Recientes Procesados";
 $recenteventsnoeventstext = "Su cuenta no ha recibido eventos en los &#249;ltimos 10 d&#237;as.";

# Definitions for search.php

$searchbreadcrumbtext = "Sistema de Notificaci&#243;n Para Temblores / Encuentre Su Cuenta o Contrase&#241;a";
 $searchheadertext = "";
 $searchpwdresettext = "<br>Hemos reinicializado su contrase&#241;a y le hemos enviado un mensaje a su primera direcci&#243;n de correo electr&#243;nico con su nueva contrase&#241;a.<br> Por favor con&#233;ctese y cambie su contrase&#241;a a algo familiar.<br>";
 $searchusernametext = "<br>Hemos enviado un correo electr&#243;nico a %s con su nombre de usuario<br>";

?>

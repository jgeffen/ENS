<?php

$SITE_TITLE = 'Earthquake Hazards Program';
$SITE_URL = 'earthquake.usgs.gov';


$SITE_DESCRIPTION = 'USGS Earthquake Hazards Program, responsible for' .
    ' monitoring, reporting, and researching earthquakes and' .
    ' earthquake hazards';

$SITE_KEYWORDS = 'aftershock,earthquake,epicenter,fault,foreshock,geologist,' .
    'geophysics,hazard,hypocenter,intensity,intensity scale,magnitude,' .
    'magnitude scale,mercalli,plate,richter,seismic,seismicity,seismogram,' .
    'seismograph,seismologist,seismology,subduction,tectonics,tsunami,quake,' .
    'sismologico,sismologia';

$SITE_SITENAV =
    navItem('https://earthquake.usgs.gov/', 'Home') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/earthquakes', 'Earthquakes') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/hazards', 'Hazards') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/science', 'Science') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/monitoring', 'Monitoring') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/education', 'Education') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/data', 'Data') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/maps', 'Maps') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/multimedia', 'Multimedia') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/publications', 'Publications') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/tools', 'Web Tools') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/software', 'Software') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/news', 'News') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/connect', 'Connect') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/partners', 'Partners') .
    navItem('https://www.usgs.gov/programs/earthquake-hazards/about', 'About')
;

$SITE_COMMONNAV = '
    <a href="https://www.usgs.gov/policies-and-notices">Legal</a>
';


// add site css
if (!isset($HEAD)) {
  $HEAD = '';
}
$HEAD = '<link rel="stylesheet" href="/theme/site/earthquake/index.css"/>' . $HEAD;

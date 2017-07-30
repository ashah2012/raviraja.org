<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "contact@raviraja.org" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "7a5b52" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'07FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA0NDkMRYAxgaXYEyyOpEpmCKBbQytLIixMBOilq6atrS0JWhWUjuA6oLYMXQy+iALiYyhbUBXYw1QARDjNEBU2ygwo+KEIv7AMQeyFrVwfzYAAAAAElFTkSuQmCC',
			'9A03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIQ6IImJTGEMYQhldAhAEgtoZW1ldHRoEEERE2l0bQhoCEBy37Sp01amropamoXkPlZXFHUQ2CoaChJDNk8AaJ4jmh0iU0QaHdDcwhoAFENz80CFHxUhFvcBACsZzTSSUVimAAAAAElFTkSuQmCC',
			'B3C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgNYQxhCHVqRxQKmiLQyOgRMdUAWa2VodG0QCAhAUcfQytrA6CCC5L7QqFVhS1etzJqG5D40dUjmYRNDtwPTLdjcPFDhR0WIxX0AwFrNdOazk2EAAAAASUVORK5CYII=',
			'F04A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZAhgaHVqRxQIaGEMYWh2mOqCIsbYyTHUICEARE2l0CHR0EEFyX2jUtJWZmZlZ05DcB1Ln2ghXhxALDQwNQbcDQx3QLRhiIDejig1U+FERYnEfANhCzWdHXeIVAAAAAElFTkSuQmCC',
			'2572' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QwQ2AMAgA6YMNcB/cAJPycRr66AbVDfx0StuXGH1qUkhIuAC5APURBiPlL34ok6LKxo5RoVZFxDHJnS1MfjtThMRG3m/fjnrUuno/aVOlT167gVsvkG8uRmlmKJ6RYcZ+wTHVENGCxgH+92G++J1Vj8wJegz/fAAAAABJRU5ErkJggg==',
			'F9BE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGUMDkMQCGlhbWRsdHRhQxEQaXRsCMcUQ6sBOCo1aujQ1dGVoFpL7AhoYA10xzGPAYh4LFjFsbsF080CFHxUhFvcBAHLCzG5q/lksAAAAAElFTkSuQmCC',
			'3222' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGaY6IIkFTGFtZXR0CAhAVtkq0ujaEOgggiw2haHRoSGgQQTJfSujVi1dtTJrVRSy+6YAYStILbJ5DAFgURQxRgewKKpbGsCiKG4WDXUNDQwNGQThR0WIxX0AfXnLcv8457IAAAAASUVORK5CYII=',
			'9AF4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDAxoCkMREpjCGsDYwNCKLBbSytgLFWlHFRBpdGximBCC5b9rUaStTQ1dFRSG5j9UVpI7RAVkvQ6toKFAsNARJTABiHppbMMVYAzDFBir8qAixuA8A0RvNrlJ3pKIAAAAASUVORK5CYII=',
			'34E0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RAMYWllDHVqRxQKmMExlbWCY6oCsspUhFCgWEIAsNoXRlbWB0UEEyX0ro5YuXRq6MmsasvumiLQiqYOaJxrqiiEGdAuaHUC3tKK7BZubByr8qAixuA8Ab2fK1W/FX5gAAAAASUVORK5CYII=',
			'B35D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDHUMdkMQCpoi0sjYwOgQgi7UyNLoCxURQ1DG0sk6Fi4GdFBq1KmxpZmbWNCT3gdQxNASi6gWa54BFzBVdDOgWRkdHFLeA3MwQyoji5oEKPypCLO4DAIhQzHxSRsv+AAAAAElFTkSuQmCC',
			'01BA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGVqRxVgDGANYGx2mOiCJiUxhDWBtCAgIQBILaAXqbXR0EEFyX9RSIApdmTUNyX1o6hBiDYGhISh2gMVQ1LEGYOpldGANZQ1lRBEbqPCjIsTiPgDc3slkuqS8vgAAAABJRU5ErkJggg==',
			'DCFD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDA0MdkMQCprA2ujYwOgQgi7WKNIDERNDEWBFiYCdFLZ22amnoyqxpSO5DU4dXDMMOLG4Bu7mBEcXNAxV+VIRY3AcACbzMzWAZT9QAAAAASUVORK5CYII=',
			'3443' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7RAMYWhkaHUIdkMQCpjBMZWh1dAhAVtnKEMow1aFBBFlsCqMrQ6BDQwCS+1ZGLV26MjNraRay+6aItLI2wtVBzRMNdQ0NQDWvFewWFDGgW4BiqG7B5uaBCj8qQizuAwBolM0654OvqQAAAABJRU5ErkJggg==',
			'F1E5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHUMDkMQCGhgDWBsYHRhQxFixiDGAxFwdkNwXGrUqamnoyqgoJPdB1DE0iGDoxSbG6IBFXQCq+1hDWUMdpjoMgvCjIsTiPgD7lcnWk28DzwAAAABJRU5ErkJggg==',
			'E4F2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nM2QsQ3AIAwETcEGZB8aehe4YZpPwQZhBBqmhHRYUCYS/u700p9MbTnQSfnFTyJlK1z8xBhULIhZM7Ew3ilmwujBTX6Saq3SWpr8GC6P3q03LgmgTHrj7T0bxoszjMQD/vdhNn4dnjHMeZrwbU8AAAAASUVORK5CYII=',
			'7A44' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZAhgaHRoCkEVbGUMYWh0aUcVYWxmmOrSiiE0RaXQIdJgSgOy+qGkrMzOzoqKQ3MfoINLo2ujogKyXtUE01DU0MDQESUykAWgemlsCiBQbqPCjIsTiPgCrCs9me2QFjQAAAABJRU5ErkJggg==',
			'D5F8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDA6Y6IIkFTBFpYG1gCAhAFmsFiTE6iKCKhSCpAzspaunUpUtDV03NQnJfQCtDoyuGeSAxDPMwxaawtqK7JTSAEWQvipsHKvyoCLG4DwDsec2N1AwxGAAAAABJRU5ErkJggg==',
			'FC7F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1lDA0NDkMQCGlgbHRoCHRhQxEQasIkxNDrCxMBOCo2atmrV0pWhWUjuA6ubwoipNwBTzNEBXYy10bUBXQzoZjSxgQo/KkIs7gMA63LLo8tnTFsAAAAASUVORK5CYII=',
			'B51A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMLQiiwVMEWlgCGGY6oAs1irSwBjCEBCAqi6EYQqjgwiS+0Kjpi5dNW1l1jQk9wVMYWh0QKiDmgcWCw1BtQNT3RTWVgY0sdAAxhDGUEcUsYEKPypCLO4DANpOzKo43AAZAAAAAElFTkSuQmCC',
			'2999' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bQh0EEHWjSoGcdO0pUszM6OiwpDdF8AY6BASMBVZL6MDQ6NDQ0ADshhrA0ujY0MAih0iDZhuCQ3FdPNAhR8VIRb3AQBvBcueLT3dbgAAAABJRU5ErkJggg==',
			'626D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0iDS6Njg6iCCLNTAAxRhhYmAnRUatWrp06sqsaUjuC5nCMIXVEU1vK0MAa0MgmhijA7oY0C0N6G5hDRANdUBz80CFHxUhFvcBAA5Py0g1c8IKAAAAAElFTkSuQmCC',
			'16FA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA1qRxVgdWFtZGximOiCJiTqINALFAgJQ9Io0sIJJhPtWZk0LWxoKJJHcx+gg2oqkDqa30bWBMTQEUwxNHSuGXtEQoJvRxAYq/KgIsbgPAMZEx8AxUeaMAAAAAElFTkSuQmCC',
			'EB9F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGUNDkMQCGkRaGR0dHRhQxRpdGwLRxVpZEWJgJ4VGTQ1bmRkZmoXkPpA6hhAMvY0OmOY1OmKxA90tUDejiA1U+FERYnEfAOyryxU9WQ+hAAAAAElFTkSuQmCC',
			'0582' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QMQ7AIAgAcfAH9D84dKeJLH0NDv5An+DiK6sbph3bpJBAcoFwAfotFP6Un/g52gQEKhnmGdUFYjYMC6rXg9AwzhjHnKLxO1ttXUY3fpwhhUCJll1I+6zrjckKLC4+T5fV2UUQJ/EH/3sxH/wumwXLv1dz+zIAAAAASUVORK5CYII=',
			'BAD8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGaY6IIkFTGEMYW10CAhAFmtlbWVtCHQQQVEn0ujaEABTB3ZSaNS0lamroqZmIbkPTR3UPNFQV3TzWkHqsNiB5pbQAKAYmpsHKvyoCLG4DwAHFc+OzLZkOQAAAABJRU5ErkJggg==',
			'C33C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WENYQxhDGaYGIImJtIq0sjY6BIggiQU0MjQ6NAQ6sCCLNTC0MjQ6OiC7L2rVqrBVU1dmIbsPTR1MDGweAwE7sLkFm5sHKvyoCLG4DwCFp8x7FqgqWgAAAABJRU5ErkJggg==',
			'D5DA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDGVqRxQKmiDSwNjpMdUAWawWKNQQEBKCKhbA2BDqIILkvaunUpUtXRWZNQ3JfQCtDoytCHbJYaAiqeZjqprC2sjY6ooiFBjCGsIYyoogNVPhREWJxHwD/Xs5NUevtLwAAAABJRU5ErkJggg==',
			'7797' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGUNDkEVbGRodHR0aRNDEXBsCUMWmMLSyAsUCkN0XtWraysyolVlI7mN0YAhgCAloRbaXFSTaEDAFWUwEJNoQEIAsBrKREegYdDGGUEYUsYEKPypCLO4DAI6fy2m2qjaLAAAAAElFTkSuQmCC',
			'9FE8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DHaY6IImJTBFpYG1gCAhAEgtoBYkxOohgiMHVgZ00berUsKWhq6ZmIbmP1RXTPAYs5glgEcPmFtYAoBiamwcq/KgIsbgPAEOdy1be5E/pAAAAAElFTkSuQmCC',
			'8306' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WANYQximMEx1QBITmSLSyhDKEBCAJBbQytDo6OjoIICijqGVtSHQAdl9S6NWhS1dFZmaheQ+qDoM81yBekWw2CFCwC3Y3DxQ4UdFiMV9AKg5y9c7K4WcAAAAAElFTkSuQmCC',
			'BD7B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDA0MdkMQCpoi0MjQEOgQgi7WKNDoAxURQ1TU6NDrC1IGdFBo1bWXW0pWhWUjuA6ubwohpXgAjqnlAMUcHRnQ7WlkbUPWC3dzAiOLmgQo/KkIs7gMAmSjN3+6WVJMAAAAASUVORK5CYII=',
			'3484' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYWhlCGRoCkMQCpjBMZXR0aEQWA6libQhoRRGbwugKVDclAMl9K6OWLl0VuioqCtl9U0RaGR0dHVDNEw11bQgMDUG1oxVoB7pbgHodUMSwuXmgwo+KEIv7AA/7zPtKvOKJAAAAAElFTkSuQmCC',
			'70A3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkMZAhimMIQ6IIu2MoYwhDI6BKCIsbYyOjo0iCCLTRFpdG0IaAhAdl/UtJWpq6KWZiG5j9EBRR0YsjYAxUIDUMwTaWBtZW1AFQtoYAxhbQhEcQuQHQBUh+rmAQo/KkIs7gMA0lrNCyfDNEQAAAAASUVORK5CYII=',
			'0EB5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDGUMDkMRYA0QaWBsdHZDViUwBijUEoogFtILVuToguS9q6dSwpaEro6KQ3AdR59Aggq63IQBFDGaHCIZbHAKQ3QdxM8NUh0EQflSEWNwHANmjy1Ilo0JGAAAAAElFTkSuQmCC',
			'80E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHaY6IImJTGEMYW1gCAhAEgtoZW1lbWB0EEFRJ9LoilAHdtLSqGkrU0NXTc1Cch+aOqh5IDFU87DbgekWbG4eqPCjIsTiPgBlwcuSuL2NywAAAABJRU5ErkJggg==',
			'A606' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQximMEx1QBJjDWBtZQhlCAhAEhOZItLI6OjoIIAkFtAq0sDaEOiA7L6opdPClq6KTM1Ccl9Aq2grUB2KeaGhIo2uQL0iqOY1OgLtQBXDdEtAK6abByr8qAixuA8AfVjMBWXRqGgAAAAASUVORK5CYII=',
			'62A7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYQximMIaGIImJTGFtZQgF0khiAS0ijY6ODqhiDQyNrkAyAMl9kVGrli5dFbUyC8l9IVMYprA2BLQi2wvkBbCGBkxBFWN0AKoLYEB1SwNrQ6ADqptFQ13RxAYq/KgIsbgPAANxzM3IHkiJAAAAAElFTkSuQmCC',
			'AA4D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YAhgaHUMdkMRYAxhDGFodHQKQxESmsLYyTHV0EEESC2gVaXQIhIuBnRS1dNrKzMzMrGlI7gOpc21E1RsaKhrqGhqIaV4jFjsaUd0CFUNx80CFHxUhFvcBAP+AzW3mat1zAAAAAElFTkSuQmCC',
			'F151' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHVqRxQIaGANYGximooqxgsRCUcWAeqcywPSCnRQatSpqaWbWUmT3gdQByVZ0vdjEWLGIMTqiu481FOiS0IBBEH5UhFjcBwCaGssGHPV4DAAAAABJRU5ErkJggg==',
			'2C12' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsQ3AMAgEcZENGAhvQGGaTIMLb0A8hKcMcUWUlIlkXjQveE7AeJTCSvqFb+MkYHBQ8NC2SgWYg8cNNZdEGLfd813FyNe79xh75OM5V+ONmWTQbiyeRHYlBhZ1FgOOnkhyZSkL/O9DvfCd1GTL6SKDHhkAAAAASUVORK5CYII=',
			'0DA1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1EQximMLQii7EGiLQyhDJMRRYTmSLS6OjoEIosFtAq0ugKJJHdF7V02spUIInsPjR1CLFQVDGQHejqQG5hRRMDuRkoFhowCMKPihCL+wA8mc1UzX/+8AAAAABJRU5ErkJggg==',
			'0396' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGaY6IImxBoi0Mjo6BAQgiYlMYWh0bQh0EEASC2hlaGUFiiG7L2rpqrCVmZGpWUjuA6ljCAlEMQ8o1ugA1CuCZocjmhg2t2Bz80CFHxUhFvcBALVOyxTbcljZAAAAAElFTkSuQmCC',
			'FB06' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNFQximMEx1QBILaBBpZQhlCAhAFWt0dHR0EEBTx9oQ6IDsvtCoqWFLV0WmZiG5D6oOwzxXoF4RLHaIEHQLppsHKvyoCLG4DwB/Q81XGys/NwAAAABJRU5ErkJggg==',
			'46C7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpI37pjCGMIQ6hoYgi4WwtjI6BDSIIIkxhog0sjYIoIixThFpYAXSAUjumzZtWtjSVatWZiG5L2CKaCtQXSuyvaGhIo2uQNtR3QISEwhAFQO5JdABi5tRxQYq/KgHsbgPACyWy0yvjT1LAAAAAElFTkSuQmCC',
			'64AB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYWhmmMIY6IImJTGGYyhDK6BCAJBbQAhRxdHQQQRZrYHRlbQiEqQM7KTJq6dKlqyJDs5DcFzJFpBVJHURvq2ioa2ggqnmtDGB1IqhuwdALcjNQDMXNAxV+VIRY3AcAr77ME7DBJjQAAAAASUVORK5CYII=',
			'9DDC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGaYGIImJTBFpZW10CBBBEgtoFWl0bQh0YMEihuy+aVOnrUxdFZmF7D5WVxR1ENiKKSaAxQ5sbsHm5oEKPypCLO4DAOLlzK6DZq9tAAAAAElFTkSuQmCC',
			'F4F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMZWllDA0NDkMQCGhimsgJpEVSxUEwxRldWiBzcfaFRS5cuDV21MgvJfQENIq1Ada0MKHpFQ10bGKagijGA1AVgijE6EBIbqPCjIsTiPgBCY8wL7jnl5QAAAABJRU5ErkJggg==',
			'4240' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpI37pjCGMDQ6tKKIhbC2MrQ6THVAEmMMEWkEigQEIImxTgHqDHR0EEFy37Rpq5auzMzMmobkvoApDFNYG+HqwDA0lCGANTQQRQzoFgegiSh2AHU2MDSiuoVhimioA7qbByr8qAexuA8Axj3MvGbgv3IAAAAASUVORK5CYII=',
			'6AA8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEx1QBITmcIYwhDKEBCAJBbQwtrK6OjoIIIs1iDS6NoQAFMHdlJk1LSVqauipmYhuS9kCoo6iN5W0VDX0EBU81pB6lDFRLDoZQ0Ai6G4eaDCj4oQi/sAFvXOPvcuy08AAAAASUVORK5CYII=',
			'0643' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHUIdkMRYA1hbGVodHQKQxESmiDQyTHVoEEESC2gF8gIdGgKQ3Be1dFrYysyspVlI7gtoFW1lbYSrg+ltdA0NQDEPZIdDI6odYLc0oroFm5sHKvyoCLG4DwBNV80uFZyUsQAAAABJRU5ErkJggg==',
			'6C3A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxlDGVqRxUSmsDa6NjpMdUASC2gRaXBoCAgIQBZrEGlgaHR0EEFyX2TUtFWrpq7MmobkvpApKOogeltBvMDQEDQxh4ZAFHUQt6DqhbiZEUVsoMKPihCL+wDpKM1pXfcB2AAAAABJRU5ErkJggg==',
			'3667' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGUNDkMQCprC2Mjo6NIggq2wVaWRtQBObItLAClKP5L6VUdPClk5dtTIL2X1TRFtZHR1aGdDMcwXahEUsgAHDLY4OWNyMIjZQ4UdFiMV9AFOSy1qeWFNNAAAAAElFTkSuQmCC',
			'12EB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUMdkMRYHVhbWYEyAUhiog4ija5AMREUvQxgsQAk963MWrV0aejK0Cwk9wHVTUE3DygWwIphHqMDphhrA4ZbQkRDXdHcPFDhR0WIxX0AiK7Hp9YEIMoAAAAASUVORK5CYII=',
			'0E3C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB1EQxlDGaYGIImxBog0sDY6AEmEmMgUEC/QgQVJLKAVKNbo6IDsvqilU8NWTV2Zhew+NHUIMaB5DATswOYWbG4eqPCjIsTiPgDIcMsV7n4F3wAAAABJRU5ErkJggg==',
			'074A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQx0aHVqRxVgDGIAiDlMdkMREpgDFpjoEBCCJBbQytDIEOjqIILkvaumqaSszM7OmIbkPqC6AtRGuDirG6MAaGhgagmIHawMDmjrWABEMMUYHTLGBCj8qQizuAwAhzMvb8c+NDgAAAABJRU5ErkJggg==',
			'CEBD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WENEQ1lDGUMdkMREWkUaWBsdHQKQxAIagWINgQ4iyGINEHUiSO6LWjU1bGnoyqxpSO5DU4cQQzcPix3Y3ILNzQMVflSEWNwHAN+ly/VkuoBKAAAAAElFTkSuQmCC',
			'B5FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDA0NDkMQCpog0sDYwOiCrC2jFIjZFJARJDOyk0KipS5eGrgzNQnJfwBSGRlcM87CJiWCKTWFtRbc3NIAxBF1soMKPihCL+wBsXsqjaWgENQAAAABJRU5ErkJggg==',
			'31D6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7RAMYAlhDGaY6IIkFTGEMYG10CAhAVtnKGsDaEOgggCw2hQEshuy+lVGropauikzNQnYfRB2aeRC9IgTEAkB60dwiCnQxupsHKvyoCLG4DwCPlcoiXJo4CAAAAABJRU5ErkJggg==',
			'258F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANEQxlCGUNDkMREpog0MDo6OiCrC2gVaWBtCEQRY2gVCUFSB3HTtKlLV4WuDM1Cdl8AQ6MjmnmMDgyNrmjmsTaIYIgBbW1Fd0toKGMI0M2obhmg8KMixOI+AC1VyOqx0bjBAAAAAElFTkSuQmCC',
			'6E3B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WANEQxmB0AFJTGSKSANro6NDAJJYQIsIkAx0EEEWawDyEOrAToqMmhq2aurK0Cwk94VMQVEH0duKxTwsYtjcgs3NAxV+VIRY3AcAM2nMOZMCSmAAAAAASUVORK5CYII=',
			'A15F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHUNDkMRYAxgDWEEySGIiU1gxxAJagXqnwsXATopaCkSZmaFZSO4DqWNoCETRGxqKKQY2D4sYo6MjmhhrKEMoqlsGKvyoCLG4DwBcBMexsztO4gAAAABJRU5ErkJggg==',
			'F827' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUNDkMQCGlhbGR0dGkRQxEQaXUEkmjoQGYDkvtColWGrVmatzEJyH1gdCKKZ5zCFYQqGWABDAAO6WxwYHVDFGENYQwNRxAYq/KgIsbgPAAu+zKF25XThAAAAAElFTkSuQmCC',
			'A597' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM3QwQ2AIAxA0fbABnUf2KAHemEDnaIe2EBH8IBTirc2etQoTTi8BPJT2C9H4U/zSh/GQUBQsrHApJiikjFaSIOyM66UT2PTV7Z1a2Npk+njCnPM/TZvRbopL+D/m5MyewsVU4reMPdmZ1/t78G56TsAYSDMborD0zIAAAAASUVORK5CYII=',
			'B690' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGVqRxQKmsLYyOjpMdUAWaxVpZG0ICAhAUSfSwNoQ6CCC5L7QqGlhKzMjs6YhuS9gimgrQwhcHdw8hwZMMUcMOzDdgs3NAxV+VIRY3AcAmUzNZZpCRAAAAAAASUVORK5CYII=',
			'6D1B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WANEQximMIY6IImJTBFpZQhhdAhAEgtoEWl0BIqJIIs1iDQ6TIGrAzspMmrayqxpK0OzkNwXMgVFHURvK0RMhIAY2C1oekFuZgx1RHHzQIUfFSEW9wEABO3MKEjfU2gAAAAASUVORK5CYII=',
			'8928' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bQh0EEFRJ9Lo0BAAUwd20tKopUuzVmZNzUJyn8gUxkCHVgY08xgaHaYwopgX0MrS6BDAiGYH0C0OqHpBbmYNDUBx80CFHxUhFvcBAHPEzFId7/IRAAAAAElFTkSuQmCC',
			'4543' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpI37poiGMjQ6hDogi4WINDC0OjoEIIkxgsSmOjSIIImxThEJYQh0aAhAct+0aVOXrszMWpqF5L6AKQyNro1wdWAYCrTVNTQAxTyGKSKNDo0OaGKsrQyNqG5hmMIYguHmgQo/6kEs7gMABm3N0+ChKUAAAAAASUVORK5CYII=',
			'842B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGUMdkMREpjBMZXR0dAhAEgsAqmJtCHQQQVHH6MoAFAtAct/SqKVLV63MDM1Ccp/IFJFWhlZGNPNEQx2mMKKYB7SjlSGAEc0OkE5UvSA3s4YGorh5oMKPihCL+wA96MqeXIozrgAAAABJRU5ErkJggg==',
			'7C82' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMZQxlCGaY6IIu2sjY6OjoEBKCIiTS4NgQ6iCCLTRFpYHR0aBBBdl/UtFWrQoEUkvsYHcDqGpHtYAXqYm0IaEV2i0gDyI6AKchiAQ0Qt6CKgdzMGBoyCMKPihCL+wB728x9zgVhTgAAAABJRU5ErkJggg==',
			'59C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYQxhCHaY6IIkFNLC2MjoEBASgiIk0ujYIOoggiQUGgMQYYOrATgqbtnRp6qpVU7OQ3dfKGIikDirGANTLiGJeQCsLhh0iUzDdwhqA6eaBCj8qQizuAwACzsytE4wmmgAAAABJRU5ErkJggg==',
			'A340' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1YQxgaHVqRxVgDRFoZWh2mOiCJiUwBqprqEBCAJBYAVMUQ6OggguS+qKWrwlZmZmZNQ3IfSB1rI1wdGIaGMjS6hgaiiAHVNTo0otsh0gq2GUUM080DFX5UhFjcBwCKtM2Zb2APVgAAAABJRU5ErkJggg==',
			'8B2B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUMdkMREpoi0Mjo6OgQgiQW0ijS6NgQ6iKCpYwCKBSC5b2nU1LBVKzNDs5DcB1bXyohhnsMURhTzwGIBjBh2MDqg6gW5mTU0EMXNAxV+VIRY3AcAR3zLbQgTXOQAAAAASUVORK5CYII=',
			'6C54' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDHRoCkMREprA2ujYwNCKLBbSINADFWlHEGkQaWKcyTAlAcl9k1LRVSzOzoqKQ3BcyRQRIBjqg6G0Fi4WGoIm5Ak1Fd4ujI6r7QG5mCGVAERuo8KMixOI+AF+ezsVzsrfMAAAAAElFTkSuQmCC',
			'7DC7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNFQxhCHUNDkEVbRVoZHQIaRFDFGl0bBFDFpoDEGBoCkN0XNW1l6qpVK7OQ3MfoAFbXimwvawNYbAqymAhYTCAAWQzoCqBbAh1QxcBuRhEbqPCjIsTiPgCUw8xb7vZB3wAAAABJRU5ErkJggg==',
			'AB3F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB1EQxhDGUNDkMRYA0RaWRsdHZDViUwRaXRoCEQRC2gVaWVAqAM7KWrp1LBVU1eGZiG5D00dGIaGYjUPqx3obgloBbsZRWygwo+KEIv7ABTNy36reiHmAAAAAElFTkSuQmCC',
			'53F9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDA6Y6IIkFNIi0sjYwBASgiDE0ujYwOoggiQUGMADVwcXATgqbtipsaeiqqDBk97WC1DFMRdYLFAOax9CALBYAEUOxQ2QKpltYA4BuBpqH7OaBCj8qQizuAwBuh8t5OeI2oQAAAABJRU5ErkJggg==',
			'6BFF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA0NDkMREpoi0sjYwOiCrC2gRaXRFF2tAUQd2UmTU1LCloStDs5DcF4LNvFYs5mERw+YWsJvRxAYq/KgIsbgPAD97ybRznSXYAAAAAElFTkSuQmCC',
			'9939' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGaY6IImJTGFtZW10CAhAEgtoFWl0aAh0EEEXa3SEiYGdNG3q0qVZU1dFhSG5j9WVMdCh0WEqsl6GVgageQENyGICrSwgMRQ7sLkFm5sHKvyoCLG4DwAr+MzwVXS0HwAAAABJRU5ErkJggg==',
			'3471' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RAMYWllDA1qRxQKmMEwFklNRVLYyhALFQlHEpjC6MjQ6wPSCnbQyaunSVSCI7L4pIq0MUxhaUc0TDXUIQBdjaGV0YEB3SytrA6oY2M0NDKEBgyD8qAixuA8An7rLl7APHmUAAAAASUVORK5CYII=',
			'4424' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpI37pjC0MoQyNAQgi4UwTGV0dGhEFmMMYQhlbQhoRRZjncLoCtQ5JQDJfdOmLV26amVWVBSS+wKmiLQytDI6IOsNDRUNdZjCGBqC7pYANLdMAenEFGMNDUAVG6jwox7E4j4ArPbMrkWHTaUAAAAASUVORK5CYII=',
			'7BA8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNFQximMEx1QBZtFWllCGUICEAVa3R0dHQQQRabItLK2hAAUwdxU9TUsKVAIgvJfYwOKOrAkLVBpNE1NBDFPBGQWAOqWEADpt6ABtEQoBiqmwco/KgIsbgPABTSzUC7BhnlAAAAAElFTkSuQmCC',
			'DFE8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QgNEQ11DHaY6IIkFTBFpYG1gCAhAFmsFiTE6iGCIwdWBnRS1dGrY0tBVU7OQ3IemjoB5aGJY3BIaABRDc/NAhR8VIRb3AQC3Qc1dLYUVZgAAAABJRU5ErkJggg==',
			'C8D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGRoCkMREWllbWRsdGpHFAhpFGl0bAlpRxBqA6hoCpgQguS9q1cqwpauioqKQ3AdRF+iAqhdkXmBoCKYd2NyCIobNzQMVflSEWNwHABhVz2Bn81y4AAAAAElFTkSuQmCC',
			'666B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0iDSyNjg6iCCLNYg0sDYwwtSBnRQZNS1s6dSVoVlI7guZItrKim5eq0ija0MgqnlYxLC5BZubByr8qAixuA8ALZHLfrmMKiUAAAAASUVORK5CYII=',
			'5165' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM3QMQ6AIAyF4cfgDThQGdxLYh04DYs3EG/gIKe0OpXoqAl0+9LAH1AfJ6On+aVPJjDECRvj7NiFQGhs4CG3FhlqbiTTN2817eVIyfYtuhcoe/vyZXqrNb4tkjW/QluIbZ+WCASFOvi/D+el7wRtIMlAg3nryQAAAABJRU5ErkJggg==',
			'444F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37pjC0MjQ6hoYgi4UwTGVodXRAVscYwhDKMBVVjHUKoytDIFwM7KRp05YuXZmZGZqF5L6AKSKtrI2oekNDRUNdQwMdsLiFPLGBCj/qQSzuAwBYI8nz2o2x8AAAAABJRU5ErkJggg==',
			'B58A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGVqRxQKmiDQwOjpMdUAWaxVpYG0ICAhAVRfC6OjoIILkvtCoqUtXha7MmobkvoApDI2OCHVQ8xgaXRsCQ0NQ7QCJoaqbwtrKiKY3NIAxhCGUEUVsoMKPihCL+wAyt8zjugdRFQAAAABJRU5ErkJggg==',
			'5A95' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nM2QsQ2AMAwEnSIbhH3sIr0jxUiwAVuYIhsAO8CUpHQEJUjxd6eX/mS4HqfQU37xkwwM4oQNY3XZESE0zBevqWGJwxo1RTR+43GcyzLNs/UrYcXMGuxyGQS1ZVx7VDcsC1tlhGz9fN1FgR07+N+HefG7AflnzFpBjRU6AAAAAElFTkSuQmCC',
			'9538' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQxlDGaY6IImJTBFpYG10CAhAEgtoFQGSgQ4iqGIhDAh1YCdNmzp16aqpq6ZmIbmP1RWoCs08hlagGJp5Aq0iGGIiU1hb0d3CGsAYgu7mgQo/KkIs7gMAa3DNIza7cvYAAAAASUVORK5CYII=',
			'E70F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMIaGIIkFNDA0OoQyOjCgiTk6OqKLtbI2BMLEwE4KjVo1bemqyNAsJPcB1QUgqYOKMTpgirE2MGLYIdLAgOaW0BCg2BRUsYEKPypCLO4DAIWPypkbdKuBAAAAAElFTkSuQmCC',
			'DA02' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QgMYAhimMEx1QBILmMIYwhDKEBCALNbK2sro6OgggiIm0ujaENAgguS+qKXTVqauigJChPug6hpR7GgVDQWKtTKgmQe0YgqK2BSRRgegW1DdDBSbwhgaMgjCj4oQi/sAE1LOumc98LMAAAAASUVORK5CYII=',
			'454B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpI37poiGMjQ6hjogi4WINDC0OjoEIIkxgsSmOjqIIImxThEJYQiEqwM7adq0qUtXZmaGZiG5L2AKQ6NrI6p5oUBbXUMDUcxjmCLS6NDoiCbG2sqAppdhCmMIhpsHKvyoB7G4DwAdv8xJd+E3gAAAAABJRU5ErkJggg==',
			'CE90' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WENEQxlCGVqRxURaRRoYHR2mOiCJBTSKNLA2BAQEIIs1gMQCHUSQ3Be1amrYyszIrGlI7gOpYwiBq0OINaCJAe1gRLMDm1uwuXmgwo+KEIv7APuRzAuilrAeAAAAAElFTkSuQmCC',
			'311C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RAMYAhimMEwNQBILmMIYwBDCECCCrLKVNYAxhNGBBVlsCkgvowOy+1ZGrYpaNW1lFor7UNVBzcMthmxHAFgvqltEA1hDGUMdUNw8UOFHRYjFfQDr2MgO3HTWXgAAAABJRU5ErkJggg==',
			'E9CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhCHVqRxQIaWFsZHQKmOqCIiTS6NggEBGCIMTqIILkvNGrp0tRVK7OmIbkvoIExEEkdVIwBpDc0BEWMBSgmiKYO5JZAFDGImx1RxAYq/KgIsbgPADhxzNBwlXoHAAAAAElFTkSuQmCC',
			'386B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGUMdkMQCprC2Mjo6OgQgq2wVaXRtcHQQQRYDqmNtYISpAztpZdTKsKVTV4ZmIbsPpA6reYGo5mERw+YWbG4eqPCjIsTiPgDLIssgbcCtVAAAAABJRU5ErkJggg==',
			'19C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCHVqRxVgdWFsZHQKmIouJOog0ujYIhKLqBYkxwPSCnbQya+nS1FWrliK7D2hHIJI6qBhDI6YYC8gONDGwW1DEREPAbg4NGAThR0WIxX0AowPJazy2YJoAAAAASUVORK5CYII=',
			'1F89' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGaY6IImxOog0MDo6BAQgiYkCxVgbAoEksl6QOkeYGNhJK7Omhq0KXRUVhuQ+iDqHqeh6WRsCGrCIYbEDzS0hQBVobh6o8KMixOI+ANCDyMvbsmFnAAAAAElFTkSuQmCC',
			'AE6C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGaYGIImxBog0MDo6AEmEmMgUkQbWBkcHFiSxgFaQGKMDsvuilk4NWzp1ZRay+8DqHB0dkO0NDQXpDUQRg5gXiGEHulsCWjHdPFDhR0WIxX0AfUTLJGdoueoAAAAASUVORK5CYII=',
			'7D8B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGUMdkEVbRVoZHR0dAlDFGl0bAh1EkMWmiDQ6ItRB3BQ1bWVW6MrQLCT3MTqgqAND1gZM80SwiAU0YLoloAGLmwco/KgIsbgPAH4py8SmCICBAAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>
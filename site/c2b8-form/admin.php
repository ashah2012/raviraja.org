<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "raviraja220381@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "19e5e7" );

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
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
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
    if( !phpfmg_user_isLogin() ){
        exit;
    };

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
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function __construct(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }

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

    function __construct( $text = '', $len = 4 ){
        $this->phpfmgImage( $text, $len );
    }

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
			'3E63' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7RANEQxmA0AFJLGCKSAOjo6NDALLKVpEG1gaHBhFksSkgMaB6JPetjJoatnTqqqVZyO4DqXN0aMA0LwDVPCxi2NyCzc0DFX5UhFjcBwAVucwlqavYBQAAAABJRU5ErkJggg==',
			'C971' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDA1qRxURaWYH8gKnIYgGNIo0ODQGhKGINQLFGB5hesJOiVi1dmrV01VJk9wU0MAY6TGFoRdXL0OgQgCbWyNLo6MCA4RbWBlQxsJsbGEIDBkH4URFicR8AcznM/nGmxeQAAAAASUVORK5CYII=',
			'0DF7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA0NDkMRYA0RaWYG0CJKYyBSRRlc0sYBWiFgAkvuilk5bmRq6amUWkvug6loZMPVOYcC0IwBZDOIWRgcMN6OJDVT4URFicR8Ast/Lhw8dqe4AAAAASUVORK5CYII=',
			'6E8E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7WANEQxlCGUMDkMREpog0MDo6OiCrC2gRaWBtCEQVa0BRB3ZSZNTUsFWhK0OzkNwXgs28VizmYRHD5hZsbh6o8KMixOI+APOzyaR0h3u8AAAAAElFTkSuQmCC',
			'F5A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMEx1QBILaBBpYAhlCAhAE2N0dHQQQRULYYWohrsvNGrq0qWrooAQ4T6gOY2uDQGNqHYAxUIDWhlQzQOpm4IqxtoKtCMAVYwRaG9gaMggCD8qQizuAwD5wc6rVN/GZAAAAABJRU5ErkJggg==',
			'D97E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDA0MDkMQCprC2MjQEOiCrC2gVaXTAJtboCBMDOylq6dKlWUtXhmYhuS+glTHQYQojml6GRocAdDEWoGloYkC3sDagioHd3MCI4uaBCj8qQizuAwBllcwTmKp2XwAAAABJRU5ErkJggg==',
			'681D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYQximMIY6IImJTGFtZQhhdAhAEgtoEWl0BIqJIIs1ANVNgYuBnRQZtTJs1bSVWdOQ3BcyBUUdRG+rSKMDEWIiUL3IbgG5mTHUEcXNAxV+VIRY3AcApRHLFfyz+agAAAAASUVORK5CYII=',
			'7DC8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNFQxhCHaY6IIu2irQyOgQEBKCKNbo2CDqIIItNAYkxwNRB3BQ1bWXqqlVTs5Dcx+iAog4MWRtAYowo5ok0YNoR0IDploAGLG4eoPCjIsTiPgDzA8zj/vSA9wAAAABJRU5ErkJggg==',
			'EBE2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDHaY6IIkFNIi0sjYwBASgijW6NjA6iGCqaxBBcl9o1NSwpaGrVkUhuQ+qrtEBwzyGVgZMsSkMWNyC6WbH0JBBEH5UhFjcBwCgzs1N/D6ZJwAAAABJRU5ErkJggg==',
			'99A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WAMYQximMEx1QBITmcLayhDKEBCAJBbQKtLo6OjoIIIm5toQ0CCC5L5pU5cuTV0VBYQI97G6MgYC1TUi28HQytDoGhrQiuwWgVYWkHlTGNDcwtoQEIDuZtaGwNCQQRB+VIRY3AcASxHNJUGn58AAAAAASUVORK5CYII=',
			'B901' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYQximMLQiiwVMYW1lCGWYiiLWKtLo6OgQiqpOpNEVKIPsvtCopUtTV0UtRXZfwBTGQCR1UPMYGjHFWEB2YHMLihjUzaEBgyD8qAixuA8ANFTN183iWqgAAAAASUVORK5CYII=',
			'DFCD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgNEQx1CHUMdkMQCpog0MDoEOgQgi7WKNLA2CDqIYIgxwsTATopaOjVs6aqVWdOQ3IemjoAYmh1Y3BIaAFSB5uaBCj8qQizuAwDcesyu/uD3UwAAAABJRU5ErkJggg==',
			'D734' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQx1DGRoCkMQCpjA0ujY6NKKItTI0OgBJNDGQ6JQAJPdFLV01bdXUVVFRSO4DqgtgaHR0QNXL6MDQEBgagiLGCiLR3CLSwAqyGcXNIg2MaG4eqPCjIsTiPgC7NNCA00/5hwAAAABJRU5ErkJggg==',
			'F722' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGaY6IIkFNDA0Ojo6BASgibk2BDqIoIq1gkgRJPeFRq2atmpl1qooJPcB1QUAVTai2sHowDAFpB9ZjBWkcgqqmAhIZQC6GGtoYGjIIAg/KkIs7gMAmIbNFvjZuJ4AAAAASUVORK5CYII=',
			'06F1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA1qRxVgDWFtZGximIouJTBFpBIqFIosFtIo0AMVgesFOilo6LWxp6KqlyO4LaBVtRVIH09voiiYGsgNdDOoWFDGwm4FuCRgE4UdFiMV9AFl5ys6+vThWAAAAAElFTkSuQmCC',
			'0B94' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGRoCkMRYA0RaGR0dGpHFRKaINLo2BLQiiwW0irSyNgRMCUByX9TSqWErM6OiopDcB1LHEBLogKa30aEhMDQEzQ5HoEuwuAVFDJubByr8qAixuA8A773Np45cPsgAAAAASUVORK5CYII=',
			'AF81' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGVqRxVgDRBoYHR2mIouJTBFpYG0ICEUWC2gFq4PpBTspaunUsFWhq5Yiuw9NHRiGhoLNa0U3D5sYul6QGEMoQ2jAIAg/KkIs7gMABRTMZhACio8AAAAASUVORK5CYII=',
			'948F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGUNDkMREpjBMZXR0dEBWFwBUxdoQiCbG6IqkDuykaVOXLl0VujI0C8l9rK4irejmMbSKhrqimSfQytCKbgfQLRh6oW5GNW+Awo+KEIv7AN0lyJnoBEbnAAAAAElFTkSuQmCC',
			'74B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZWlmBGEW0lWEqa6PDVAdUsVDWhoCAAGSxKYyurI2ODiLI7otaunRp6MqsaUjuY3QQaUVSB4asDaKhrg2BKGJAdiu6HQEgMTS3gMXQ3TxA4UdFiMV9ABCFzEN7gMgiAAAAAElFTkSuQmCC',
			'4D22' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QMQ6AIAxFy8AN6n3q4F4TungETgEDN0DuIKcUt4qOmvD/9tKkLx/qIwFG6j9+eXIgsJNmDpOZiVkx4zAuYSVUzGaMFDig8iulHP7wdVN+fN0liPqHSGMZ0t2lMYbcsWQIuHe2soobYb/v+uJ3AqE3zIYKeMVLAAAAAElFTkSuQmCC',
			'92C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCHaY6IImJTGFtZXQICAhAEgtoFWl0bRB0EEERYwCKMcDUgZ00beqqpUtXrZqaheQ+VleGKawIdRDYyhDA2sCIYp4A0FZWNDuAbmlAdwtrgGioA5qbByr8qAixuA8AzeDLvDpmFXQAAAAASUVORK5CYII=',
			'9682' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWlkbQh0EEEVawCqaxBBct+0qdPCVoWuWhWF5D5WV1GQeY3IdjAAzXMFmoDsFgGI2BQGLG7BdDNjaMggCD8qQizuAwCPh8ulnC9MJwAAAABJRU5ErkJggg==',
			'A5FA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDA1qRxVgDRBpYGximOiCJiUwBiwUEIIkFtIqEsAJNEEFyX9TSqUuXhq7MmobkPqDpja4IdWAYGgoWCw1BNQ9DXUAraysrhhhjCLrYQIUfFSEW9wEAO4zLh5ibkHwAAAAASUVORK5CYII=',
			'9AD8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGaY6IImJTGEMYW10CAhAEgtoZW1lbQh0EEERE2l0bQiAqQM7adrUaStTV0VNzUJyH6srijoIbBUNdUUzTwBsHqqYyBSgGJpbWAOAYmhuHqjwoyLE4j4A1HHNuTxja7AAAAAASUVORK5CYII=',
			'7B4E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNFQxgaHUMDkEVbRVoZWh0dGFDFGh2moolNAaoLhItB3BQ1NWxlZmZoFpL7GB1EWlkbUfWyNog0uoYGooiJAMUc0NQFNADtwBDD4uYBCj8qQizuAwCvFMsdTRxj2AAAAABJRU5ErkJggg==',
			'4C4E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37pjCGMjQ6hgYgi4WwNjq0Ojogq2MMEWlwmIoqxjpFpIEhEC4GdtK0adNWrczMDM1Ccl8AUB1rI6re0FCgWGigA6pbgHagqWOYAnQLhhgWNw9U+FEPYnEfAAnwy1/zaI90AAAAAElFTkSuQmCC',
			'BC1D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYQxmmMIY6IIkFTGFtdAhhdAhAFmsVaXAEiomgqAPypsDFwE4KjZq2atW0lVnTkNyHpg5uHjYxB3QxkFumoLoF5GbGUEcUNw9U+FERYnEfANS/zMbPQQ2OAAAAAElFTkSuQmCC',
			'245D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYWllDHUMdkMREpjBMZW1gdAhAEgtoZQgFiYkg625ldGWdCheDuGna0qVLMzOzpiG7L0CklaEhEEUvo4Mo0E5UMVagiaxoYiIgWxwdUdwSGsoAdA0jipsHKvyoCLG4DwBlPMn/Z5O/QgAAAABJRU5ErkJggg==',
			'CBAD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WENEQximMIY6IImJtIq0MoQyOgQgiQU0ijQ6Ojo6iCCLAVWyNgTCxMBOilo1NWzpqsisaUjuQ1MHE2t0DUUTA9rhiqYO5BaQXmS3gNwMFENx80CFHxUhFvcBAJVBzMIZ2q3FAAAAAElFTkSuQmCC',
			'042F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGUNDkMRYAximMjo6OiCrE5nCEMraEIgiFtDK6MqAEAM7KWrp0qWrVmaGZiG5L6BVpJWhlRFNr2iowxRGdDtaGQJQxYBuAepEFQO5mTUU1S0DFX5UhFjcBwA4hcgTnnVksgAAAABJRU5ErkJggg==',
			'3626' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGaY6IIkFTGFtZXR0CAhAVtkq0sjaEOgggCw2RQRIBjogu29l1LSwVSszU7OQ3TdFtJWhlRHDPIcpjA4i6GIBqGJgtzgwoOgFuZk1NADFzQMVflSEWNwHAHCCytDXctxxAAAAAElFTkSuQmCC',
			'8D66' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaY6IImJTBFpZXR0CAhAEgtoFWl0bXB0EEBVBxRjdEB239KoaStTp65MzUJyH1idoyMW8wIdRAiIYXMLNjcPVPhREWJxHwDw8czgdL/wMwAAAABJRU5ErkJggg==',
			'78F6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA6Y6IIu2srayNjAEBKCIiTS6NjA6CCCLTQGpY3RAcV/UyrCloStTs5Dcx+gAVodiHmsDxDwRJDERLGIBDZhuCWgAurmBAdXNAxR+VIRY3AcAdsbK7OCLEO8AAAAASUVORK5CYII=',
			'91FA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA1qRxUSmMAawNjBMdUASC2hlBYkFBKCIAfU2MDqIILlv2tRVUUtDV2ZNQ3IfqyuKOgiE6A0NQRITaMVUJzIFUwzoklAM8wYo/KgIsbgPAH7myCRvTzlNAAAAAElFTkSuQmCC',
			'9BD1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGVqRxUSmiLSyNjpMRRYLaBVpdG0ICEUTa2UFksjumzZ1atjSVVFLkd3H6oqiDgIh5qGICWARg7oFRQzq5tCAQRB+VIRY3AcAZ33NLMwuwKgAAAAASUVORK5CYII=',
			'5349' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNYQxgaHaY6IIkFNIi0MrQ6BASgiIFUOTqIIIkFBjC0MgTCxcBOCpu2KmxlZlZUGLL7gKaxAnUj6wWKNbqGAm1CtgMo5tDogGKHyBQRkCiKW1gDMN08UOFHRYjFfQBqV80eAPFiWQAAAABJRU5ErkJggg==',
			'BB93' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGUIdkMQCpoi0Mjo6OgQgi7WKNLo2BDSIoKljBYoFILkvNGpq2MrMqKVZSO4DqWMIgauDm+eAbh5QzBGLHehuwebmgQo/KkIs7gMASKTOs69a1S4AAAAASUVORK5CYII=',
			'ED6A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGVqRxQIaRFoZHR2mOqCKNbo2OAQEYIgxOogguS80atrK1Kkrs6YhuQ+sztERpg5Jb2BoCKYYujqgW1D1QtzMiCI2UOFHRYjFfQD/M82AH9rhpgAAAABJRU5ErkJggg==',
			'0241' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHVqRxVgDWFsZWh2mIouJTBFpdJjqEIosFtAK1BkI1wt2UtTSVUtXZmYtRXYfUN0UVjQ7gGIBrKEBrah2MDpgcUsDuhijg2ioQ6NDaMAgCD8qQizuAwB7/Mx07hH3FQAAAABJRU5ErkJggg==',
			'EEA8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMEx1QBILaBBpYAhlCAhAE2N0dHQQQRNjbQiAqQM7KTRqatjSVVFTs5Dch6YOIRYaiMU8bGKoekFuBoqhuHmgwo+KEIv7APdQzdbQ/lwkAAAAAElFTkSuQmCC',
			'F2B7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGUNDkMQCGlhbWRsdGkRQxEQaXUEkihhDoytQXQCS+0KjVi1dGrpqZRaS+4DyU4DmtTKg6g1gbQiYgirG6AAUC0AVY21gbXR0QBUTDXUNZUQRG6jwoyLE4j4AfK/Ny2FBXvAAAAAASUVORK5CYII=',
			'6915' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYQximMIYGIImJTGFtZQhhdEBWF9Ai0uiILtYg0ugwhdHVAcl9kVFLl2ZNWxkVheS+kCmMgQ5TgOYi621laMQUYwGZ5yCC7pYpDAHI7gO5mTHUYarDIAg/KkIs7gMAOezLrJfMQCQAAAAASUVORK5CYII=',
			'1F16' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMEx1QBJjdRBpYAhhCAhAEhMFijGGMDoIoOgFqpvC6IDsvpVZU8NWTVuZmoXkPqg6FPNgekWIEkNzSwjQLaEOKG4eqPCjIsTiPgAuP8hSu540zgAAAABJRU5ErkJggg==',
			'F38E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGUMDkMQCGkRaGR0dHRhQxBgaXRsC0cWQ1YGdFBq1KmxV6MrQLCT3oanDZx4WMWxuwXTzQIUfFSEW9wEAmInLAL+sokAAAAAASUVORK5CYII=',
			'3C20' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RAMYQxlCGVqRxQKmsDY6OjpMdUBW2SrS4NoQEBCALDZFBEgGOogguW9l1DQgkZk1Ddl9IHWtjDB1cPMYpmCKOQQwoNgBdosDA4pbQG5mDQ1AcfNAhR8VIRb3AQD6AMvyhFkA8QAAAABJRU5ErkJggg==',
			'F2D5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGUMDkMQCGlhbWRsdHRhQxEQaXRsC0cQYQGKuDkjuC41atXTpqsioKCT3AdVNYQWZgKo3AFOM0YEVaAeqGGsDa6NDAKr7RENdQxmmOgyC8KMixOI+AMWlzaH73uCYAAAAAElFTkSuQmCC',
			'CB3E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7WENEQxhDGUMDkMREWkVaWRsdHZDVBTSKNDo0BKKKAVUyINSBnRS1amrYqqkrQ7OQ3IemDiaGaR4WO7C5BZubByr8qAixuA8ApuHLyWJxov0AAAAASUVORK5CYII=',
			'C895' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WEMYQxhCGUMDkMREWllbGR0dHZDVBTSKNLo2BKKKNbC2sjYEujoguS9q1cqwlZmRUVFI7gOpYwgJaBBB0SvS6NCAJga0wxFohwiGWxwCkN0HcTPDVIdBEH5UhFjcBwCficvxgS+hcwAAAABJRU5ErkJggg==',
			'4887' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37pjCGMIQyhoYgi4WwtjI6OjSIIIkxhog0ujYEoIixToGoC0By37RpK8NWha5amYXkvgCIulZke0NDweZNQXULWCwAVQyk19EBi5tRxQYq/KgHsbgPAEYqy2G7pWI2AAAAAElFTkSuQmCC',
			'1123' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUIdkMRYHRgDGB0dHQKQxEQdWANYGwIaRND1AsUCkNy3MmtVFJBYmoXkPrC6VoaGAHS9UxgwzQvAFGN0YER1SwhrKGtoAIqbByr8qAixuA8AyifHD3D6LGsAAAAASUVORK5CYII=',
			'F6B5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGUMDkMQCGlhbWRsdHRhQxEQaWRsC0cUagOpcHZDcFxo1LWxp6MqoKCT3BTSIAs1zAKpGNc8VbAK6WKCDCIZbHAJQ3QdyM8NUh0EQflSEWNwHAHHHzX2Jqp/BAAAAAElFTkSuQmCC',
			'407C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2Quw2AMAxEzxLZgIHMBkZKlmAKU7CByQYUYUo+lSMoQeDrnk66J2O9nOJPecfPICHJLJ5FilCR1jGKYYL23DgWrB157Nj75ZzLsJTB+8nRM2K/m9LOpGawMBFTtQHblxWVy+msqJ2/+t9zufHbAFwjypgAIy5kAAAAAElFTkSuQmCC',
			'8E7C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA6YGIImJTBEBkgEBIkhiAa0gXqADC7q6RkcHZPctjZoatmrpyixk94HVTWF0YEA3LwBTjNGBEcMOVqBKZLeA3dzAgOLmgQo/KkIs7gMAAc7LAyKMmV4AAAAASUVORK5CYII=',
			'6237' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUNDkMREprC2sjY6NIggiQW0iABFAlDFGhgaHcCiCPdFRq1aumrqqpVZSO4LmcIwBaiyFdnegFaGACA5BVWM0QFIBjCguqWBtdHRAdXNoqGOoYwoYgMVflSEWNwHAICjzPlAc4GrAAAAAElFTkSuQmCC',
			'886B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0ijS6Njg6iKCpY21ghKkDO2lp1MqwpVNXhmYhuQ+sDqt5gSjmYRPD5hZsbh6o8KMixOI+AGApy5ycUOxhAAAAAElFTkSuQmCC',
			'4A20' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpI37pjAEMIQytKKIhTCGMDo6THVAEmMMYW1lbQgICEASY50i0ujQEOggguS+adOmrcxamZk1Dcl9ASB1rYwwdWAYGioa6jAFVYwBpC6AAcUOkJijAwOKW0BirqEBqG4eqPCjHsTiPgCkfMwXrFBunQAAAABJRU5ErkJggg==',
			'D862' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGaY6IIkFTGFtZXR0CAhAFmsVaXRtcHQQQRFjbWUF0iJI7otaujJs6VQgjeQ+sDpHh0YHDPMCWhkwxaYwYHELppsZQ0MGQfhREWJxHwCVGs4XIydDowAAAABJRU5ErkJggg==',
			'7D47' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkNFQxgaHUNDkEVbRVoZWh0aRFDFGh2moolNAYoFOjQEILsvatrKzMyslVlI7mN0EGl0bXRoRbaXtQEoFhowBVlMBCjm0OgQgCwW0AB0S6OjA6oY2M0oYgMVflSEWNwHAPG+zXhYP4/LAAAAAElFTkSuQmCC',
			'5339' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNYQxhDGaY6IIkFNIi0sjY6BASgiDE0OjQEOoggiQUGMLQyNDrCxMBOCpu2KmzV1FVRYcjuawWpc5iKrBcsArIJ2Q6IGIodIlMw3cIagOnmgQo/KkIs7gMAYSLNFhFQCMwAAAAASUVORK5CYII=',
			'8345' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANYQxgaHUMDkMREpoi0MrQ6OiCrC2hlaHSYiiomMoWhlSHQ0dUByX1Lo1aFrczMjIpCch9IHWujQ4MImnmuQFvRxRwaHR1E0N3S6BCA7D6Imx2mOgyC8KMixOI+AIyIzLJ9qn1sAAAAAElFTkSuQmCC',
			'FB66' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGaY6IIkFNIi0Mjo6BASgijW6Njg6CKCpY21gdEB2X2jU1LClU1emZiG5D6zO0RGLeYEOIoTFsLgF080DFX5UhFjcBwCJI81l427MPwAAAABJRU5ErkJggg==',
			'DBE1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHVqRxQKmiLSyNjBMRRFrFWl0bWAIRRMDqYPpBTspaunUsKWhq5Yiuw9NHbJ5hMWmYOqFujk0YBCEHxUhFvcBAOQIzZwnO9g1AAAAAElFTkSuQmCC',
			'11AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YAhimMIY6IImxOjAGMIQyOgQgiYk6sAYwOjo6iKDpZW0IhImBnbQya1XU0lWRWdOQ3IemDiEWikUMmzqgGIpbQlhDgWIobh6o8KMixOI+AOwTxqgV/TC+AAAAAElFTkSuQmCC',
			'A3FE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDA0MDkMRYA0RaWYEyyOpEpjA0uqKJBbQyIKsDOylq6aqwpaErQ7OQ3IemDgxDQ7Gah0UM0y0BrUA3NzCiuHmgwo+KEIv7AOsGydNM+8vnAAAAAElFTkSuQmCC',
			'96DB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUMdkMREprC2sjY6OgQgiQW0ijSyNgQ6iKCKNYDEApDcN23qtLClqyJDs5Dcx+oq2oqkDgKB5rmimSeARQybW7C5eaDCj4oQi/sAvevL1NZgV5UAAAAASUVORK5CYII=',
			'BB5E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHUMDkMQCpoi0sjYwOiCrC2gVaXRFFwOpmwoXAzspNGpq2NLMzNAsJPeB1DE0BGKY54BFzBVdDKiX0dERRQzkZoZQRhQ3D1T4URFicR8ApsLLxoKWLCcAAAAASUVORK5CYII=',
			'FF22' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGaY6IIkFNIg0MDo6BASgibE2BDqIoInBSJj7QqOmhq1ambUqCsl9YBWtDI3odjBMAYqiiwUARdHd4gAURXdLaGBoyCAIPypCLO4DAHrxzRwMisVFAAAAAElFTkSuQmCC',
			'A9CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCHUNDkMRYA1hbGR0CHZDViUwRaXRtEEQRC2gFiTHCxMBOilq6dGnqqpWhWUjuC2hlDERSB4ahoQyN6GIBrSxY7MB0C9A8kJtRxAYq/KgIsbgPALKxylNKBFAyAAAAAElFTkSuQmCC',
			'9A14' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMDQEIImJTGEMYQhhaEQWC2hlbQWKtqKKiTQ6TGGYEoDkvmlTp63MmrYqKgrJfayuIHWMDsh6GVpFQ4FioSFIYgIQ89DcginGGiDS6BjqgCI2UOFHRYjFfQDdPM3ZWIBIvQAAAABJRU5ErkJggg==',
			'13C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1YQxhCHVqRxVgdRFoZHQKmIouJOjA0ujYIhKLqZWhlbWCA6QU7aWXWqrClq1YtRXYfmjqYGNA8bGICaGJgt6CIiYaA3RwaMAjCj4oQi/sAJ0bJEscq1yAAAAAASUVORK5CYII=',
			'7F7F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkNFQ11DA0NDkEVbRYBkoAMDIbEpQLFGR5gYxE1RU8NWLV0ZmoXkPkYHoLopjCh6WRuAYgGoYiJAyOiAKhYAFGNtICw2UOFHRYjFfQAUq8lpQV5+nAAAAABJRU5ErkJggg==',
			'22AE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQximMIYGIImJTGFtZQhldEBWF9Aq0ujo6IgixtDK0OjaEAgTg7hp2qqlS1dFhmYhuy+AYQorQh0YAk0PYA1FFWMFiqKrEwGKoouFhoqGAu1FcfNAhR8VIRb3AQBHYMoGeEhnoQAAAABJRU5ErkJggg==',
			'8142' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHaY6IImJTGEMYGh1CAhAEgtoBaqc6ugggqIOqDfQoUEEyX1Lo1ZFrczMWhWF5D6QOtZGh0YHFPOAYqFAEk0M6JYpDOh2NDoEoLqZNZSh0TE0ZBCEHxUhFvcBABaOy0hmJYphAAAAAElFTkSuQmCC',
			'7346' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNZQxgaHaY6IIu2irQytDoEBKCIgVQ5Ogggi00BigY6OqC4L2pV2MrMzNQsJPcxOjC0sjY6opjH2sDQ6Boa6CCCJAZkNzo0OqKIBTSIgGxG0RvQgMXNAxR+VIRY3AcATZbMcozrYl8AAAAASUVORK5CYII=',
			'D829' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGaY6IIkFTGFtZXR0CAhAFmsVaXRtCHQQQRFjbWVAiIGdFLV0ZdiqlVlRYUjuA6trZZgqgmaewxSGBgyxAAZUO0BucWBAcQvIzayhAShuHqjwoyLE4j4Aew/NN0FqkoIAAAAASUVORK5CYII=',
			'8154' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHRoCkMREpjAGsDYwNCKLBbSygsRaUdUB9U5lmBKA5L6lUauilmZmRUUhuQ+kjqEh0AHVPLBYaAiaGCvQJeh2MDqiug/oklCGUAYUsYEKPypCLO4DAIPey7LeUWOAAAAAAElFTkSuQmCC',
			'011C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAhimMEwNQBJjDWAMYAhhCBBBEhOZAhQNYXRgQRILaAXpBZqA5L6opauiVk1bmYXsPjR1OMVEpkDEkO1gDQC7D8UtjA6soYyhDihuHqjwoyLE4j4Af/nHvKyO7TsAAAAASUVORK5CYII=',
			'E594' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNEQxlCGRoCkMQCGkQaGB0dGtHFWBsCWtHEQoBiUwKQ3BcaNXXpysyoqCgk9wHlGx1CAh1Q9QLFGgJDQ1DNa3QEkqjqWFuBbkERCw1hDEF380CFHxUhFvcBANAOzyiwquJ2AAAAAElFTkSuQmCC',
			'5FFD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkNEQ11DA0MdkMQCGkQaWBsYHQKwiIkgiQUGoIiBnRQ2bWrY0tCVWdOQ3deKqRebWAAWMZEpmG5hhdiL4uaBCj8qQizuAwB698qej+o5AQAAAABJRU5ErkJggg==',
			'577E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNEQ11DA0MDkMSA7EaHhkAHBgJigQEMrQyNjjAxsJPCpq2atmrpytAsZPe1MgQwTGFE0cvQCuQHoIoFtLI2MDqgiolMEWkAiSKLsQaAxVDcPFDhR0WIxX0AV/7KMBuE+zMAAAAASUVORK5CYII=',
			'6EA2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WANEQxmmMEx1QBITmSLSwBDKEBCAJBbQItLA6OjoIIIs1iDSwAomEe6LjJoatnRVFBAi3BcyBayuEdmOgFagWGhAKwO6WEPAFAY0twDFAtDdzNoQGBoyCMKPihCL+wATa80WOn5KsAAAAABJRU5ErkJggg==',
			'7670' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA1pRRFtZgfyAqQ4oYiKNQLGAAGSxKSINDI2ODiLI7ouaFrZq6cqsaUjuY3QQbWWYwghTB4asDSKNDgGoYiJAMUcHBhQ7AhpYW1kbGFDcEtAAdDPQRYMh/KgIsbgPAMk0y81Z8RjOAAAAAElFTkSuQmCC',
			'0D2C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaYGIImxBoi0Mjo6BIggiYlMEWl0bQh0YEESC2gVaXQAiiG7L2rptJVZKzOzkN0HVtfK6MCArncKqhjIDocARhQ7wG5xYEBxC8jNrKEBKG4eqPCjIsTiPgCynsrw7/ZHpQAAAABJRU5ErkJggg==',
			'2F7F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DA0NDkMREpogAyUAHZHUBrZhiDCCxRkeYGMRN06aGrVq6MjQL2X0BQHVTGFH0MjoAxQJQxVgbRIDiqGIiQMjagCoWGoopNlDhR0WIxX0AxY7JDln2bLkAAAAASUVORK5CYII=',
			'92FA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA1qRxUSmsLayNjBMdUASC2gVaXRtYAgIQBFjAIoxOogguW/a1FVLl4auzJqG5D5WV4YprAh1ENjKEAAUCw1BEhNoZXRAVwd0SwO6GGuAaKgrunkDFH5UhFjcBwBR3cpYAnLkwgAAAABJRU5ErkJggg==',
			'AA9A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGVqRxVgDGEMYHR2mOiCJiUxhbWVtCAgIQBILaBVpdG0IdBBBcl/U0mkrMzMjs6YhuQ+kziEErg4MQ0NFQx0aAkND0MxzbEBVBxZzdMQQcwhlRBEbqPCjIsTiPgBNrMyxeSMUwwAAAABJRU5ErkJggg==',
			'B847' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYQxgaHUNDkMQCprC2MrQ6NIggi7WKNDpMRRMDqQt0aAhAcl9o1MqwlZlZK7OQ3AdSx9ro0MqAZp5raMAUdDGHRocABnQ7Gh0dsLgZRWygwo+KEIv7ACxrzl7FzzAbAAAAAElFTkSuQmCC',
			'3D28' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RANEQxhCGaY6IIkFTBFpZXR0CAhAVtkq0ujaEOgggiw2RaTRoSEApg7spJVR01ZmrcyamoXsPpC6VgYM8xymMKKaBxILQBUDu8UBVS/IzayhAShuHqjwoyLE4j4AodjMeNeQJJUAAAAASUVORK5CYII=',
			'6521' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGVqRxUSmiDQwOjpMRRYLaBFpYG0ICEURaxAJAZIwvWAnRUZNXbpqZdZSZPeFTGFodGhFtQOoq9FhCrqYSKNDALpbWFsZHVDFWAMYQ1hDA0IDBkH4URFicR8AEO7MIWhnCYQAAAAASUVORK5CYII=',
			'E5E4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHRoCkMQCGkQaWBsYGrGItaKJhQDFpgQguS80aurSpaGroqKQ3AeUb3RtYHRA1QsWCw1BNQ8oxoDmFtZWVjSx0BDGEHQ3D1T4URFicR8AIoDOmqEf2YQAAAAASUVORK5CYII=',
			'ABA5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB1EQximMIYGIImxBoi0MoQyOiCrE5ki0ujo6IgiFtAq0sraEOjqgOS+qKVTw5auioyKQnIfRF1AgwiS3tBQkUbXUFQxoLpG14ZABxEMOwICAlDEREOAYlMdBkH4URFicR8AlCPNNVI6UQAAAAAASUVORK5CYII=',
			'DA55' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHUMDkMQCpjCGsDYwOiCrC2hlbcUUE2l0ncro6oDkvqil01amZmZGRSG5D6TOoSGgQQRFr2gophjQvIZABxSxKSKNjo4OAcjuCw0AmhfKMNVhEIQfFSEW9wEAtIbN232jBeAAAAAASUVORK5CYII=',
			'628A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMdUASC2gRaXRtCAgIQBZrYGh0dHR0EEFyX2TUqqWrQldmTUNyX8gUhimMCHUQva0MAawNgaEhKGKMDkAxFHVAtzSg62UNEA11CGVEERuo8KMixOI+AHOVy2wgH4LgAAAAAElFTkSuQmCC',
			'568E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGUMDkMQCGlhbGR0dHRhQxEQaWRsCUcQCA0QakNSBnRQ2bVrYqtCVoVnI7msVxTCPoVWk0RXNvAAsYiJTMN3CGoDp5oEKPypCLO4DADGzyb+QdeYDAAAAAElFTkSuQmCC',
			'0BC3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCHUIdkMRYA0RaGR0CHQKQxESmiDS6Ngg0iCCJBbSKtLKCaCT3RS2dGrZ01aqlWUjuQ1MHEwOax4BiHjY7sLkFm5sHKvyoCLG4DwCpP8yakL8A0wAAAABJRU5ErkJggg==',
			'72D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM3QsRGAIAwF0FBkA9wHC/oUpGEEpwgFG7AChUwpZ5WclnqadO/+Xf4FxmUE/rSv9GN2Cdlx0lqxYgnijfkShaw1OI10vzx6H3nfVD8XoKFQ1XdRgKY1bX4mp5E2mkksa7C2cGRn7Kv/Pbg3/Q6fDMxbpRSXZwAAAABJRU5ErkJggg==',
			'9F28' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQx1CGaY6IImJTBFpYHR0CAhAEgtoFWlgbQh0EEETA5IwdWAnTZs6NWzVyqypWUjuY3UFqmtlQDGPAaR3CiOKeQIgsQBUMbBbHFD1sgYA3RIagOLmgQo/KkIs7gMAb1/LaLXyrzEAAAAASUVORK5CYII=',
			'C5B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WENEQ1lDGVqRxURaRRpYGx2mOiCJBTQCxRoCAgKQxRpEQlgbHR1EkNwXtWrq0qWhK7OmIbkPqKfRFaEOIdYQiCrWKAIUQ7VDpJW1Fd0trCGMIehuHqjwoyLE4j4A0pfNkBgTfqYAAAAASUVORK5CYII=',
			'2AB0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGVqRxUSmMIawNjpMdUASC2hlbWVtCAgIQNbdKtLo2ujoIILsvmnTVqaGrsyahuy+ABR1YMjoIBrq2hCIIsbaAFSHZocISAzNLaGhQDE0Nw9U+FERYnEfAO4BzRhbbP+IAAAAAElFTkSuQmCC',
			'6E3F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WANEQxmBMARJTGSKSANro6MDsrqAFhEgGYgq1gAUQ6gDOykyamrYqqkrQ7OQ3BcyBUUdRG8rFvOwiGFzC9TNKGIDFX5UhFjcBwAu7Mp8hArELQAAAABJRU5ErkJggg=='        
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
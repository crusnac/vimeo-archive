<?php
/*
* Plugin Name: Vimeo Archive
* Description: Displays videos from a Vimeo Album
* Version: 1.0
* Author: Claud Rusnac
* Author URI: http://www.srcreative.co
*/
define( 'vimeo_archive_path', plugin_dir_path( __FILE__ ));

//Include the CSS and JS files
function vimeo_archive_files(){
    wp_enqueue_style( 'vimeo-archive', plugins_url('/css/vimeo-archive.css', __FILE__), false, '1.0.0', 'all');
    wp_enqueue_script('vimeo-archive', plugin_dir_url(__FILE__) . 'js/vimeo-archive.js', array('jquery'));
    }

//Enable for error reporting.
//error_reporting(E_ALL); ini_set('display_errors', 1);

add_action('wp_enqueue_scripts', 'vimeo_archive_files');

    // Display the Vimeo Archive Results. 
    function vimeo_archive_create($atts, $content = null){ 

    // begin output buffering
    ob_start();
        
    //Set Language per Translation
    //Check if English Language Is Set
    if(ICL_LANGUAGE_CODE == "en"){
        setlocale(LC_TIME,"en_EN"); date_default_timezone_set('America/Los_Angeles');
        }

    //Check if Romanian is Set
    if(ICL_LANGUAGE_CODE == "ro"){
        setlocale(LC_TIME,"ro_RO.UTF8"); date_default_timezone_set('America/Los_Angeles');
        }

    
    ?>

        <?php //Set Variables & Attributes
            $archive_page = $_GET["archive_page"];
            $album = $atts["album"]; //6002921
            $user = $atts["user"]; //'98594172' // Philadelphia Romanian Church
            $folder = $atts["folder"]; //'Specify which folder to get all videos from
            $apitoken = $atts["apitoken"]; //'98cf90a75d89f6591b5bc0fa531950e2' // Philadelphia Church App.  See developer.vimeo.com
            $videos_per_page = $atts["videos_per_page"]; // '20'
            $sort = $atts["sort"]; //'alphabetical'
            $direction = $atts["direction"]; //'desc'
            $main_title = $atts["main_title"]; //'Show Main Title on the Page'
            $stats = $atts["stats"]; //'1 = On, 0 = Off, Default = 0'
            $api_debug = $atts["debug"]; //'1 = On, 0 = Off, Default = 0'

            //Validate Critical Attributes and set default Values                  
        ?>
        

        <?php if(!isset($archive_page) || $archive_page == null ): //Check ?archive_page _GET Variable. Set Default to 1. ?>
            <?php $archive_page = '1'; ?>
        <?php endif ?>

        <?php if(isset($album)): //Check if a Ablum is set or if and if it is not empty. ?>
            <?php if($atts["album"] == null): //Check Album ID - If not set, show error ?>
                <div class="error">Please specify a valid album in your shortcode. i.e [vimeo_archive album='6002921']. See <a href="https://developer.vimeo.com" target="_blank">developer.vimeo.com</a></div>
                <?php return false; // Stop Execution because a Album wasn't set. ?>
            <?php endif ?>
        <?php endif ?>

        <?php if(isset($folder)): //Check if a Folder is set or if and if it is not empty. ?>
            <?php if($atts["folder"] == null): //Check Folder ID - If not set, show error ?>
                <div class="error">Please specify a valid folder in your shortcode. i.e [vimeo_archive folder='6002921']. See <a href="https://developer.vimeo.com" target="_blank">developer.vimeo.com</a></div>
                <?php return false; // Stop Execution because a Folder wasn't set. ?>
            <?php endif ?>
        <?php endif ?>

        <?php if(!isset($folder) && !isset($album) ): //Check if a Folder and Album is set or if and if it is not empty. ?>
            <div class="error">Please specify a valid Album or Folder in your shortcode. i.e [vimeo_archive album='6002921'] [vimeo_archive folder='1795375'] . See <a href="https://developer.vimeo.com" target="_blank">developer.vimeo.com</a></div>
            <?php return false; // Stop Execution because a Album wasn't set. ?>
        <?php endif ?>




        <?php if($atts["user"] == null): //Check $user ID - If not set, show error ?>
            <div class="error">Please specify a valid user in your shortcode. i.e [vimeo_archive user='98594172']. See <a href="https://developer.vimeo.com" target="_blank">developer.vimeo.com</a></div>
            <?php return false; // Stop Execution because a User wasn't set. ?>
        <?php endif //End $user ID ?>

        <?php if($atts["apitoken"] == null): //Check apitoken ID - If not set, show error ?>
            <div class="error">Please specify a valid API Token in your shortcode. i.e [vimeo_archive apitoken='10cf90a75d90f6538b4bc0fa531950e2']. See <a href="https://developer.vimeo.com" target="_blank">developer.vimeo.com</a></div>
            <?php return false; // Stop Execution because a apitoken wasn't set. ?>
        <?php endif //End apitoken ID ?>

        <?php if(!isset($videos_per_page)): //Set $videos_per_page default to 20. ?>
            <?php $videos_per_page = '20'; ?>
        <?php endif ?>

        <?php if(!isset($sort)): //Set $sort default to alphabetical. ?>
            <?php $sort = 'alphabetical'; ?>
        <?php endif ?>

        <?php if(!isset($direction)): //Set $direction default to desc. ?>
            <?php $direction = 'desc'; ?>
        <?php endif ?>

        <?php 
            
        // get cURL resource
        $ch = curl_init();

        if (isset($folder)) {
            $url = 'https://api.vimeo.com/users/'.$user.'/projects/'.$folder.'/videos?per_page='.$videos_per_page.'&page='.$archive_page.'&sort='.$sort.'&direction='.$direction.'';
        }else{
            $url = 'https://api.vimeo.com/users/'.$user.'/albums/'.$album.'/videos?per_page='.$videos_per_page.'&page='.$archive_page.'&sort='.$sort.'&direction='.$direction.'';
        }
        
        
        // set url.  see developer.vimeo.com
        curl_setopt($ch, CURLOPT_URL, $url);

        // set method
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        // return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // set headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          'Authorization: Bearer '.$apitoken.'',
        ]);

        // send the request and save response to $response
        $response = curl_exec($ch);
        //Convert the JSon Response to an array
        $videos = json_decode($response, true );

        // stop if fails
        if (!$response) {
            //Display Error if Issue Occurs
            echo 'API Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch);
            return false;
        }

        ?>
        
        <?php 
        //Set Variables based upon response
        $total_pages = ceil($videos["total"]/$videos_per_page);
        ?>

        <?php if($stats == '1'): //Cehck if STATS is set. ?>
        <div class="row">
            <pre>
                <h2>API Debug Stats</h2>
                <ul>
                    <li><strong>API URL:</strong> <?php echo $url; ?></li>
                    <li><strong>Total Number of Videos:</strong> <?php echo $videos['total']; ?></li>
                    <li><strong>Total Number of Videos Per page:</strong> <?php echo $videos_per_page; ?></li>
                    <li><strong>Total Number Pages:</strong> <?php echo $total_pages; ?></li>
                    <li><strong>Page #:</strong> <?php echo $archive_page ?></li>
                </ul>
                
                <h3>Parameters</h3>
                <ul>
                	<li><strong>API Token:</strong> <?php echo $apitoken; ?><li>
                	<li><strong>Debug Status:</strong> <?php echo $api_debug; ?><li>
                </ul>
                
            </pre>
            
            <?php if($api_debug == '1'): //check if DEBUG is set. ?>
            <div class="row">
            <h2>API Debug</h2>
                <pre><?php print_r($videos); ?></pre>
            </div>
        <?php endif ?>
            
        </div>
        <?php endif ?>


        <!-- Pagination -->

        <div class="row">
            
           <div class="col-xs-12 col-md-6">
                <?php if($atts["main_title"] == '0'): //Cehck if STATS is set. ?>
                    <?php else: ?>
                    <h1 class="media-services-title"><?php the_title(); ?></h1>
                <?php endif ?>
				</div>
            
            
            
            <?php if($total_pages > 1): //Only show if archive has more than one page?>
                <div class="col-xs-12 col-md-6">
                    <div class="filter-options" style="float: right;">
                    <div class="btn-group" role="group" aria-label="...">
                          <div class="btn-group" role="group">

                            <?php if($archive_page != 1): ?>
                                <a class="btn btn-outline-primary"href="?archive_page=<?php echo ($archive_page-1); ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i> <?php _e( 'Previous', 'phil_ro' ); ?></a>
                            <?php endif ?>  

                          </div>
                          <div class="btn-group" role="group">
                              <button type="button" class="btn btn-outline-primary disabled"><?php _e( 'Page', 'phil_ro' ); ?>: <?php echo $archive_page; ?>/<?php echo $total_pages; ?></button>
                          </div>
                        <div class="btn-group" role="group">
                            <?php if($archive_page != $total_pages): ?>
                                <a class="btn btn-outline-primary" href="?archive_page=<?php echo ($archive_page+1); ?>"><?php _e( 'Next', 'phil_ro' ); ?> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
                            <?php endif ?>  
                          </div>
                        </div>



                    <!-- Single button -->
                        <div class="btn-group">
                          <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php _e( 'Go to Page', 'phil_ro' ); ?> <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <?php for($i=1; $i<=$total_pages; $i++) : ?>
                                    <li><a class="<?php if($archive_page == $i): ?>active<?php endif ?>"href="?archive_page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                <?php endfor; ?>
                          </ul>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            
            

        </div>
        <!-- // Pagination -->



        <!-- Video Archive -->

        <div class="row">
			<?php $counter = 0; ?>
            
            
            <?php foreach ($videos["data"] as $video) : ?>
                <?php $counter++; ?>
            
                <!-- Modal for video <?php echo $int = (int) filter_var($video['uri'], FILTER_SANITIZE_NUMBER_INT); ?> -->
                    
                <div class="modal fade" id="service-media-<?php echo $counter; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <?php if(preg_match('/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/', $video['name'])): ?>
                                    <?php preg_match('/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/', $video['name'], $matches); ?>
                                    <?php $extracteddate = $matches[0]; ?>
                                    <?php $timeconvert = strtotime($extracteddate); ?>
                                    <?php echo strftime('%A', $timeconvert); ?>
                                <?php endif ?>
                                <h3 class="modal-title" id="myModalLabel"><?php echo $video["name"]; ?></h3>
                            </div>
                            <div class="modal-body">
                                <div class="hidden_element">
                                <iframe data-src="https://player.vimeo.com/video/<?php echo $int = (int) filter_var($video['uri'], FILTER_SANITIZE_NUMBER_INT); ?>" width="100%" height="490" frameborder="0" scrolling="no" allowfullscreen="true"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- // Modal for video <?php echo $int = (int) filter_var($video['uri'], FILTER_SANITIZE_NUMBER_INT); ?> -->
            
                <!-- Video Details -->
            
                <div class="<?php if($counter == 1): ?>col-xs-12 col-sm-12 col-md-12 service-video<?php else: ?>col-xs-12 col-sm-12 col-md-4 service-video<?php endif; ?>">
                    <div class="overlay">
                        <a href="#" data-toggle="modal" data-target="#service-media-<?php echo $counter; ?>">                                            	
                            <img class="img-responsive video" src="<?php echo $video['pictures']['sizes']['6']['link']; ?>" alt="images">
                            <i class="fa fa-play" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="video-caption">
                        
                        <?php if(preg_match('/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/', $video['name'])): ?>
                            <?php preg_match('/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/', $video['name'], $matches); ?>
                            <?php $extracteddate = $matches[0]; ?>
                            <?php $timeconvert = strtotime($extracteddate); ?>
                            <?php //echo strftime('%A', $timeconvert); ?>
                        <?php endif ?>
                    
                        <?php echo $video['name']; ?>
                    </div>  

                </div>
            
                <?php if($counter == 1): ?><?php $counter = 3; ?><?php endif; ?>
                                    
                <?php if ($counter % 3 == 0): //Add a Break every 3 videos?>
                    <div class="row"></div>
                <?php endif ?>

                <!-- // Video Details -->
            
            <?php endforeach; ?>
            
            
        </div>

        <!-- //Video Archive -->
                


        <?php // close curl resource to free up system resources 
        curl_close($ch);
        ?>



    <?php // end output buffering, grab the buffer contents, and empty the buffer
    return ob_get_clean();
    
    }// End Function
    
add_shortcode('vimeo_archive', 'vimeo_archive_create');

?>
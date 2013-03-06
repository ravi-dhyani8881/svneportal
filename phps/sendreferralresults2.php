<?php
require_once '../includes/global.inc.php';
if (!isset($_POST['action'])) { // if page is not submitted to itself echo the form
    include( "header.php");
    ?>

    <script type="text/javascript">
        
        function getTotalRecordOnAjax() {                                     
            var textselect=$('#referal option:selected').val();            
            var url="checkEmailExist.php?email="+textselect+'&method=getreferalDetails';
            $.getJSON(url, function(data) {			                                              
                $("#providername").val(data.dfirst_name +' '+data.dlast_name);
                $("#testtoperform").val(data.tests_to_perform_txt);                                    
                $("#patientname").val(data.pfirst_name +' '+data.plast_name);
                $("#organization").val(data.dorg_name);            
                $("#pdob").val(data.pDATE_OF_BIRTH);
                        
                if(data.pGENDER_REPLACE=='M'){
                    $("#gender").val("Male");
                }else{
                    $("#gender").val("Female");         
                }                        
            });                                    
        }
        
        $(function(){
            $('#swfupload-control').swfupload({
                upload_url: "upload-file.php",
                file_post_name: 'uploadfile',
                file_size_limit : "1024",
                file_types : "*.jpg;*.png;*.gif",
                file_types_description : "Image files",
                file_upload_limit : 5,
                flash_url : "../resources/js/swfupload/swfupload.swf",
                button_image_url : '../resources/js/swfupload/wdp_buttons_upload_114x29.png',
                button_width : 114,
                button_height : 29,
                button_placeholder : $('#button')[0],
                debug: false
            })
            .bind('fileQueued', function(event, file){
                var listitem='<li id="'+file.id+'" >'+
                    'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
                    '<div class="progressbar" ><div class="progress" ></div></div>'+
                    '<p class="status" >Pending</p>'+
                    '<span class="cancel" >&nbsp;</span>'+
                    '</li>';
                $('#log').append(listitem);
                $('li#'+file.id+' .cancel').bind('click', function(){
                    var swfu = $.swfupload.getInstance('#swfupload-control');
                    swfu.cancelUpload(file.id);
                    $('li#'+file.id).slideUp('fast');
                });
                // start the upload since it's queued
                $(this).swfupload('startUpload');
            })
            .bind('fileQueueError', function(event, file, errorCode, message){
                alert('Size of the file '+file.name+' is greater than limit');
            })
            .bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
                $('#queuestatus').text('Files Selected: '+numFilesSelected+' / Queued Files: '+numFilesQueued);
            })
            .bind('uploadStart', function(event, file){
                $('#log li#'+file.id).find('p.status').text('Uploading...');
                $('#log li#'+file.id).find('span.progressvalue').text('0%');
                $('#log li#'+file.id).find('span.cancel').hide();
            })
            .bind('uploadProgress', function(event, file, bytesLoaded){
                //Show Progress
                var percentage=Math.round((bytesLoaded/file.size)*100);
                $('#log li#'+file.id).find('div.progress').css('width', percentage+'%');
                $('#log li#'+file.id).find('span.progressvalue').text(percentage+'%');
            })
            .bind('uploadSuccess', function(event, file, serverData){
                var item=$('#log li#'+file.id);
                item.find('div.progress').css('width', '100%');
                item.find('span.progressvalue').text('100%');
                var pathtofile='<a href="uploads/'+file.name+'" target="_blank" >view &raquo;</a>';
                item.addClass('success').find('p.status').html('Done!!! | '+pathtofile);
            })
            .bind('uploadComplete', function(event, file){
                // upload has completed, try the next one in the queue
                $(this).swfupload('startUpload');
            })                      
                                                                
        });	

    </script>
    <style type="text/css" >
        #swfupload-control p{ margin:10px 5px; font-size:0.9em; }
        #log{ margin:0; padding:0; width:460px;}
        #log li{ list-style-position:inside; margin:2px; border:1px solid #ccc; padding:10px; font-size:12px; 
                 font-family:Arial, Helvetica, sans-serif; color:#333; background:#fff; position:relative;}
        #log li .progressbar{ border:1px solid #333; height:5px; background:#fff; }
        #log li .progress{ background:#999; width:0%; height:5px; }
        #log li p{ margin:0; line-height:18px; }
        #log li.success{ border:1px solid #339933; background:#ccf9b9; }
        #log li span.cancel{ position:absolute; top:5px; right:5px; width:20px; height:20px; 
                             background:url('../resources/js/swfupload/cancel.png') no-repeat; cursor:pointer; }
        </style>

        <tr>
            <td style="background-color:white;height:600px;width:300px;vertical-align:middle;">
                <?php
                include( "navigationd.php");

                $account_id = $_SESSION['staff_account_id'];
                $result = $db->getReferrals($account_id, '3');
                ?>
            <script>
                $(function() {
                    $( "#datepicker" ).datepicker();
                });
            </script>

        </td>
        <td style="background-color:white;width:900px;text-align:top;float:left;">
            <table style="margin: 40px 50px 40px;width:800px" cellpadding="0px" cellspacing="0px;" >


                <tr><td >

                        <span class="left-box"></span><span class="cent-box" style="width:758px;">Send Referral Results </span><span class="right-box"></span>

                    </td></tr>

                <tr><td>

                        <table class="main" style="width:100%">
                            <tr><td class="Left">
                                    <p>Select Referral:</p></td>
                                <td class="Right">
                                    <p>

                                        <select name="doctorsname" id="referal" onchange="getTotalRecordOnAjax();" class="selectBox">

                                            <option value="-1">select one </option>

                                            <?php
                                            while ($row = mysql_fetch_array($result)) {
                                                echo "<option value='$row[referral_id]'>$row[first_name]$row[last_name] </option>";
                                            }
                                            ?>

                                        </select>

                                    </p>
                                </td>
                            </tr>
                            <tr class="textBoxTable"><td class="Left">
                                    <p class="bold">
                                        Provider Information</p></td>
                                <td class="Right">
                                    <p>&nbsp;
                                    </p>
                                </td>
                            </tr>
                            <tr class="textBoxTable"><td class="Left">
                                    <p>
                                        Name:</p></td>
                                <td class="Right">
                                    <p><input class="width320" type="text" id="providername" name="providername" size="50" maxlength="50" /></p>
                                </td>
                            </tr>
                            <tr class="textBoxTable"><td class="Left">
                                    <p>
                                        Organization:</p></td>
                                <td class="Right">
                                    <p><input class="width320" type="text" id="organization" name="organization" size="50" maxlength="50" /></p>
                                </td>
                            </tr>
                            <tr class="textBoxTable"><td class="Left">
                                    <p class="bold">
                                        Patient Information</p></td>
                                <td class="Right">
                                    <p>&nbsp;
                                    </p>
                                </td>
                            </tr>
                            <tr class="textBoxTable"><td class="Left">
                                    <p>
                                        Name:</p></td>
                                <td class="Right">
                                    <p><input class="width320" type="text" id="patientname" name="patientname" size="50" maxlength="50" /></p>
                                </td>
                            </tr>
                            <tr class="textBoxTable"><td class="Left">
                                    <p>
                                        Gender:</p></td>
                                <td class="Right">
                                    <p><input class="width320" type="text" id="gender" name="gender" size="10" maxlength="10" /></p>
                                </td>
                            </tr>
                            <tr class="textBoxTable"><td class="Left">
                                    <p>
                                        Date of Birth:</p></td>
                                <td class="Right">
                                    <p><input class="width320" type="text" id="pdob" name="dob" id="datepicker" /></p>
                                </td>
                            </tr>
                            <tr class="textBoxTable"><td class="Left">
                                    <p>
                                        Test(s) to Perform:</p></td>
                                <td class="Right">
                                    <p><textarea rows="4" cols="50" id="testtoperform" name="testtoperform" class="width320">

                                        </textarea></p>
                                </td>
                            </tr>
                            <tr class="textBoxTable"><td class="Left">
                                    <p class="bold">
                                        Generic Information</p></td>
                                <td class="Right">
                                    <p>&nbsp;
                                    </p>
                                </td>
                            </tr>
                            <tr class="textBoxTable"><td class="Left">
                                    <p>
                                        Test Results:</p></td>
                                <td class="Right">
                                    <p><textarea rows="4" cols="50" name="testresults" class="width320">

                                        </textarea></p>
                                </td>
                            </tr>
                            <tr class="textBoxTable"><td class="Left">
                                    <p>
                                        Interpretations & Comments:</p></td>
                                <td class="Right">
                                    <p><textarea rows="4" cols="50" name="comments" class="width320">

                                        </textarea></p>
                                </td>
                            </tr>

                            <tr class="textBoxTable"><td class="Left">
                                    <p>
                                        Recommended Treatment:</p></td>
                                <td class="Right">
                                    <p><textarea rows="4" cols="50" name="treatment" class="width320">

                                        </textarea></p>
                                </td>
                            </tr>
                            <tr class="textBoxTable"><td class="Left">
                                    <p>Attached files:</p></td>
                                <td class="Right">
                                    <div id="swfupload-control">
                                        <p>Upload upto 5 image files(jpg, png, gif), each having maximum size of 1MB</p>
                                        <input type="button" id="button" />
                                        <p id="queuestatus" ></p>
                                        <ol id="log"></ol>
                                    </div>
                                </td>
                            </tr>
                            <tr class="textBoxTable">
                                <td colspan="2" style="float:left;margin-left:220px;">
                                    <input type="submit" name="action" value="Send Now" style="background-color: #4682B4;border-radius:5px;height: 35px; width: 100px"/>
                                    <input type="submit" name="action" value="Send Later" style="background-color: #4682B4;border-radius:5px;height: 35px; width: 100px;margin-left:20px;"/>
                                    <input type="submit" name="action" value="Cancel" style="background-color: #4682B4;border-radius:5px;height: 35px; width: 100px;margin-left:20px;"/>
                                </td></tr>

                        </table>

                    </td></tr></table>

        </td>
    </tr>


    <?php
    include( "footer.php");
    ?>
    </table>

    </form>
    </body>
    </html>
    <?php
} else {

    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'Send Now')
            $nextpage = 'testUpload.php';
        else if ($_POST['action'] == 'Send Later')
            $nextpage = 'maind.php';
        else if ($_POST['action'] == 'Cancel')
            $nextpage = 'maind.php';
        else if ($_POST['action'] == 'Attach Files')
            $nextpage = 'maind.php';
    }
    header("location:" . $nextpage);
    exit;
}
?>
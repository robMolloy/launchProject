<?php
function trigger_notice($notice){
    $type = gettype($notice);
    $noticeString = ($type=='string' ? $notice : json_encode($notice));
    $noticeString = str_replace(['<',   ','],['&lt', ', '],$noticeString);
    
    trigger_error('<span class="triggerNotice">'.strtoupper($type).': <br>'.$noticeString.'</span>');
}

function validateProject(){
    $projectName=isset($_REQUEST['projectName']) ? $_REQUEST['projectName'] : '';
    $projectLabel=isset($_REQUEST['projectLabel']) ? $_REQUEST['projectLabel'] : '';
    $projectAcronym=isset($_REQUEST['projectAcronym']) ? $_REQUEST['projectAcronym'] : '';
    $dbAcronym=isset($_REQUEST['dbAcronym']) ? $_REQUEST['dbAcronym'] : '';
    
    $valid=True;
    if(strlen($projectName)<3 || strlen($projectLabel)<3 || strlen($projectAcronym)<3 || strlen($dbAcronym)<3){$valid=False;}
    if(!ctype_alpha($projectName) || !ctype_alpha($projectLabel) || !ctype_alpha($projectAcronym) || !ctype_alpha($dbAcronym)){$valid=False;}
    if(is_dir($projectName) || file_exists($projectName)){$valid=False;}
    return $valid;
}

function launchProject(){
    $projectName=isset($_REQUEST['projectName']) ? $_REQUEST['projectName'] : '';
    $projectLabel=isset($_REQUEST['projectLabel']) ? $_REQUEST['projectLabel'] : '';
    $projectAcronym=isset($_REQUEST['projectAcronym']) ? $_REQUEST['projectAcronym'] : '';
    $dbAcronym=isset($_REQUEST['dbAcronym']) ? $_REQUEST['dbAcronym'] : '';
    
    copy_directory('defaultProject',$projectName);
    exec('sudo chown -R pi '.$projectName);
    exec('sudo chgrp -R pi '.$projectName);
    
    replaceWordInAllFilesInDirectory($projectName,'oneThing',strtolower($projectName));
    replaceWordInAllFilesInDirectory($projectName,'thing',strtolower($projectLabel));
    replaceWordInAllFilesInDirectory($projectName,'Thing',ucfirst(strtolower($projectLabel)));
    replaceWordInAllFilesInDirectory($projectName,'tng',strtolower($projectAcronym));
    replaceWordInAllFilesInDirectory($projectName,'ont',strtolower($dbAcronym));
    
    renameAllFilenamesInFolder($projectName,'thing',$projectLabel);
    renameAllFilenamesInFolder($projectName.'/img','thing',$projectLabel);
    renameAllFilenamesInFolder($projectName.'/nav','thing',$projectLabel);
    renameAllFilenamesInFolder($projectName.'/includes','thing',$projectLabel);
    renameAllFilenamesInFolder($projectName.'/includes/classes','thing',$projectLabel);
}

function copy_directory($src,$new) {
    exec('sudo cp -R '.$src.' '.$new);
}
function replaceWordInAllFilesInDirectory($dir,$find,$replace){
    exec("sudo find ".$dir." -type f -exec sed -i 's/".$find."/".$replace."/g' {} \;");
}

function renameAllFilenamesInFolder($dir,$find,$replace){
    exec("sudo rename 's/".$find."/".$replace."/' ".$dir."/*");
}

$nav = (isset($_REQUEST['nav']) ? $_REQUEST['nav'] : '');

//~ trigger_notice($nav);
switch($nav){
    case 'launchProject':
        if(validateProject()){
            launchProject();
            echo True;
        } else {
            echo False;
        }
    break;
    
    case 'validateProject':
        echo validateProject();
    break;
    
    case '':
?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="height=device-height width=device-width initial-scale=1">
    <style type="text/css">
    *       {margin:0;padding:0;color:#222222;box-sizing:border-box;}
    
    body    {background-color:#EEEEEE;font-family:'Montserrat';}
    main    {text-align:center;margin:20px auto 20px auto;}
    a       {text-decoration:none;}
     
    input[type=text], input[type=password], textarea {
        padding:7px;background-color:#EEEEEE;color:#222222;
        border:1px solid #CCCCCC;width:100%;
    }
    input[type=text]:focus, input[type=password]:focus, textarea:focus {background-color:#FFFFFF;}
    textarea {resize:vertical;height:100px;}

    button {
        background-color:#222222;border:1px solid #CCCCCC;border-radius:3px;
        color:#FFFFFF;padding:5px 10px;text-align: center;text-decoration: none;
        display:inline-block;text-transform:uppercase;font-size:16px;cursor:pointer;
    }
    
    button:hover {background-color:#EEEEEE;color:#CCCCCC;}
    
    .hidden {display:none !important;}
    .error {font-size:15px;color:#FF0000;}

    .singleColumn {display:grid;grid-template-columns:repeat(1,auto);grid-row-gap:20px;}
    .oneLineContents {display:flex;align-items:center;}
    .centerContents {display:flex;justify-content:center;align-items:center;}
    .centerContentsHorizontally {display:flex;justify-content:center;align-items:flex-start;}
    .centerContentsVertically {display:flex;align-items:center;}
    
    .wrapperMain {
        min-width:30%;max-width:80vw;text-align:center;overflow-wrap:break-word;
        /*.singleColumn*/
        display:inline-grid;grid-template-columns:repeat(1,auto);grid-row-gap:20px;
    }
    
    .panel {
        background-color:#FFFFFF;padding:20px;
        /*.singleColumn*/
        display:grid;grid-template-columns:repeat(1,auto);grid-row-gap:20px;
    }
    
    .textBlock {white-space:pre-wrap;}
    .buttonBar {text-align:center;}
    
    .displayMode {text-align:left;}
    .editMode {text-align:left;}
    
    .titleBar {display:flex;}
    .titleBar > * {display:flex;align-items:center;}
    .titleBar > *:nth-child(1) {flex:1;}
    
    #responseLogIcon {background-image:url("img/icon.png");background-size:cover;position:fixed;bottom:20px;right:20px;min-height:50px;min-width:50px;cursor:pointer;z-index:1;}
    #responseLog {background-color:#FFCCCC;position:fixed;display:inline-block;overflow-wrap:break-word;bottom:20px;right:20px;height:40vh;width:40vw;min-width:250px;border-radius:0 0 30px 0;overflow-y:auto;}
    
    @media(max-width:768px){
        header{padding:0;/****singleColumn****/flex-direction:column;justify-content:center;align-items:center;}
        header > * {/****centerContentsVertically****/display:flex;justify-content:center;width:100%}
        header button {width:100%;border:0;border-radius:0;}
        header img {max-width:100vw;height:auto;}
        
        .wrapperMain {min-width:100vw;max-width:100vw;}
    }

    </style>
    
    <script type="text/javascript">
        function ajax(params={}) {
            let file = ('file' in params ? params.file : ''); //~ !essential parameter!
            let f = ('f' in params ? params.f : new FormData());
            let nav = ('nav' in params ? params.nav : ''); //~ !pass in file or essential parameter!
            
            if(nav!=''){f.append('nav',nav);}
            
            return new Promise((resolve, reject) => {
                const request = new XMLHttpRequest();
                request.open("POST", file);
                request.onload = (()=>{
                    if (request.status == 200){
                        resolve(request.response);
                    } 
                    else {reject(Error(request.statusText));}
                });
                request.onerror = (()=>{reject(Error("Network Error"));});
                request.send(f);
            });
        }
        
        async function launchProject(){
            let launchButton = document.getElementById('launchButton');
            let validateButton = document.getElementById('validateButton');
            let panel = document.getElementById('launchNewProjectPanel');

            let valid = await validateProject();
            let success = false;
            if(valid){
                let f = getElementValues({'getValuesFrom':'launchNewProjectPanel'});
                launchButton.innerHTML = "Creating...";
                launchButton.disabled = "disabled";
                success = await ajax({'file':'launchProject.php?nav=launchProject','f':f});
            }
            if(success){
                panel.innerHTML = 'Project created successfully. Realign database to complete.<textarea>' + newDbText() + '</textarea>';
            } else {
                panel.innerHTML = 'Attempted to create project. Check if complete.';
            };
        }
        
        async function validateProject(){
            let panel = document.getElementById('launchNewProjectPanel');
            let launchButton = document.getElementById('launchButton');
            let validateButton = document.getElementById('validateButton');
            
            let valid = checkInputs();
            console.log(valid);
            if(valid){
                let f = getElementValues({'getValuesFrom':'launchNewProjectPanel'})
                valid = await ajax({'file':'launchProject.php?nav=validateProject','f':f});
            }
            if(valid){
                show(launchButton);hide(validateButton);
                panel.style.backgroundColor = "#FFFFFF";
            } else {
                hide(launchButton);show(validateButton);
                panel.style.backgroundColor = "#FF000022";
            }
            return valid;
        }
        
        function newDbText(name,label,acronym,dbacronym){
            return `CREATE DATABASE ${name}DB;
                    CREATE TABLE ${dbacronym}_${label}s (
                        ${acronym}_id int(11) NOT NULL,
                        ${acronym}_usr_id int(11) NOT NULL,
                        ${acronym}_usr_access varchar(8) NOT NULL,
                        ${acronym}_title varchar(255) NOT NULL,
                        ${acronym}_description varchar(4095) NOT NULL,
                        ${acronym}_time_added int(11) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


                    CREATE TABLE ${dbacronym}_users (
                        usr_id int(11) NOT NULL,
                        usr_first_name varchar(255) DEFAULT NULL,
                        usr_last_name varchar(255) DEFAULT NULL,
                        usr_email varchar(255) DEFAULT NULL,
                        usr_password varchar(2047) DEFAULT NULL,
                        usr_type varchar(255) DEFAULT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    ALTER TABLE ${dbacronym}_${label}s ADD PRIMARY KEY (${acronym}_id);

                    ALTER TABLE ${dbacronym}_users ADD PRIMARY KEY (usr_id);


                    ALTER TABLE ${dbacronym}_${label}s MODIFY ${acronym}_id int(11) NOT NULL AUTO_INCREMENT;

                    ALTER TABLE ${dbacronym}_users MODIFY usr_id int(11) NOT NULL AUTO_INCREMENT;`;
        }
        
        function show(elm){
            elm = initElement(elm);
            elm.style.display = "inline-block";
        }
        
        function hide(elm){
            elm = initElement(elm);
            elm.style.display = "none";
        }
        
        function checkInputs(){
            let name = document.getElementById('projectNameInput').value;
            let label = document.getElementById('projectNameLabel').value;
            let acronym = document.getElementById('projectAcronymInput').value;
            let dbacronym = document.getElementById('dbAcronymInput').value;
            let panel = document.getElementById('launchNewProjectPanel');
            let launchButton = document.getElementById('launchButton');
            let validateButton = document.getElementById('validateButton');
            
            let valid = true;
            
            if(name.length<3 || label.length<3 || acronym.length<3 || dbacronym.length<3){valid=false;}
            if(!onlyLetters(name) || !onlyLetters(label) || !onlyLetters(acronym) || !onlyLetters(dbacronym)){valid=false;}
            
            return valid;
        }
        
        function onlyLetters(str) {
            return str.match("^[A-z]+$");
        }
        
        function getElementValues(params={}){
            var f = ('f' in params && params.f!='' ? params.f : new FormData);
            var getValuesFrom = initElement('getValuesFrom' in params ? params.getValuesFrom : '');
            
            if(getValuesFrom!=='' && getValuesFrom!==undefined && getValuesFrom!==null){
                var all = getValuesFrom.querySelectorAll('input,select,textarea');
                
                var valid;
                for(var i=0; i<all.length; i++){
                    valid = true;
                    if(all[i].name==''){valid=false;}
                    if(all[i].type=='checkbox' && all[i].checked==false){valid=false;}
                    if(valid){f.append(all[i].name,all[i].value);}
                }
            }
            return f;
        }
        
        function initElement(element=''){
            return element.nodeName==undefined ? document.getElementById(element) : element;
        }
    </script>
</head>

<body>
    <main>
        <div class="wrapperMain" id="wrapperMain">
            <div class="panel" id="launchNewProjectPanel">
                <h1>Launch New Project</h1>
                <input type="text" name="projectName" placeholder="projectName" id="projectNameInput" onkeyup="validateProject();">
                <input type="text" name="projectLabel" placeholder="projectLabel" id="projectNameLabel" onkeyup="validateProject();">
                <input type="text" name="projectAcronym" placeholder="projectAcronym" id="projectAcronymInput" onkeyup="validateProject();">
                <input type="text" name="dbAcronym" placeholder="dbAcronym" id="dbAcronymInput" onkeyup="validateProject();">
                <div class="buttonBar">
                    <button id="launchButton" onclick="launchProject();" style="display:none;">Launch Project</button>
                    <button id="validateButton" onclick="validateProject();" style="display:inline-block;">Valid Project?</button>
                </div>
            </div>
        </div>
    </main>
</body>
<?php
    break;
}
?>

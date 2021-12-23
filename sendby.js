var claim_data=[];
claim_data={
	'loss':
		[
		'Copy of MAWB/HAWB',
		'Copy of Shipping invoice/Packing list',  
		'Copy of Customs certificate showing missing pcs',
		'Missing weight statement',
		'Cargo Irregularity report (CIR)',
		'Claim value (amount)',
		'Any other documents required by Carrier'
		],
	'damage':
		[
		'Copy of MAWB/HAWB',
		'Copy of Shipping invoice and Packing list',
		'Copy of damage survey report with exact description of damaged shipment',
		'Copy of irregularity report issued by Airports handlers',
		'Copy of Customs certificate showing damaged pcs',
		'Photographs of damaged contents and/or CCTV',
		'Damaged goods description and their current location for inspection',
		'Claim value (amount)',
		'Damaged weight statement',
		'Copy of POD (proof of delivery)',
		'Any other documents required by Carrier'
		],
	'delay':
	    [
	    'Copy of MAWB/HAWB',
        'Copy of shipping invoice and packing list',
        'Cargo description',
        'Claim value (amount)',
        'Any other documents required by Carrier'
		],
	'others':
	    [
		'Copy of MAWB',
        'Any other documents required by Carrier'
		]

	};

$('select[name=claim]').bind('change',getClaimVal);
function getClaimVal(){
	var x = $(this).val();

    $('form input').removeAttr("disabled");
    $('form select').removeAttr("disabled");
	$('form input[type=text]').removeAttr("style");
    $('form select').removeAttr("style");
	$('form textarea').removeAttr("disabled");
	$('form textarea[name=desc]').removeAttr("style");
    $('.claim_form form').hide();
	$('.claim_form form').eq(x).show();	
}

$('select[name=type]').bind('change',getClaimType);
function getClaimType(){
	var x = $(this).val();
	console.log(x);
	if(claim_data[x]){
		$('.upload_info').eq(0).find('ul').html('');
		$('.info').css({"display":"block"});
		$('.notice').hide();
	    $('#'+x+'_notice').css({"display":"block"});
		$('.upload_info').css({"display":"block"});

		claim_data[x].map((item,index)=>{
			$('.upload_info').eq(0).find('ul').append('<li>'+item+'</li>');                            
		});
	}

}

$('.require_t').bind('click',show_info);
function show_info()
{
	$('.upload_info').show();
}

function remove_file()
{
  var r=$(this).attr('remove-id');
  files = files.filter(x => {
	return x.id!= r;
	});
	console.log(files);
  $(this).closest('.progress_my').remove();
}


var files=[];
var inc=0;
$('input[name=file]').bind('change',file_now);
function file_now()
{
	var file_data = $(this).prop('files');
	if(!file_data[0]) return false;

    var t=$(this);
    console.log(files);
    for(var i=0;i<file_data.length;i++)
	{
		inc++;

		file_check(file_data[i],t,inc);
	}
}
function file_check(file,t,my_i)
{
    var progress=$('.progress_my').eq(0).clone();
	progress.css({'display':'flex'});
	t.parent().parent().append(progress);

    progress.find('h4').html(file.name);
	var form_data = new FormData();                  
	form_data.append('file', file,file.name);
	form_data.append('time', $('input[name=tt]').val());

	$.ajax({
		type: 'POST',
		url: '/claim_form',
		contentType: false,
		processData: false,
		data: form_data,
		dataType: 'json',
		xhr: function() {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function(evt) {
			if (evt.lengthComputable) {
				console.log(evt.loaded);
				console.log(evt.total);
				if(evt.total/(1024*1024)<1)
				{
					progress.find('.megabytes span').html((evt.loaded/(1024)).toFixed()+'<em> KB</em>');
				    progress.find('.megabytes span').html((evt.total/(1024)).toFixed()+'<em> KB</em>');
				}
				else
				{
					progress.find('.megabytes span').html((evt.loaded/(1024*1024)).toFixed(1)+'<em> MB</em>');
				    progress.find('.megabytes span').html((evt.total/(1024*1024)).toFixed(1)+'<em> MB</em>');
				}
				var percentComplete = evt.loaded / evt.total;
				percentComplete = parseInt(percentComplete * 100);
				console.log(percentComplete);
                progress.find('.progress_line').css({"width":percentComplete+'%'});
				progress.find('.progress_num span').html(percentComplete+'%');
				if (percentComplete === 100) {
					progress.find('.progress_line').hide();
					progress.find('.progress_num').hide();
					progress.find('.button').show();

					progress.find(".remove").bind('click',remove_file);
					progress.find(".remove").attr('remove-id',my_i);
					

				}

			}
			}, false);

			return xhr;
		},
		success:function(response) {
			var d=response.data;
			d.id=my_i;
			files.push(d);
			console.log(files);
		}
	});

}

$('.submit a').bind('click',send_form);

function send_form()
{
	var form_data=new FormData(document.forms[$(this).attr('form-id')]);
	var form=document.forms[$(this).attr('form-id')];

	$(form).find('select').removeAttr('style');
    $(form).find("textarea[name=desc]").removeAttr('style');
	$(form).find("input[type=text]").removeAttr('style');
	
	files.map(item=>{
	  form_data.append('file_url[]',item.url);
      form_data.append('file_name[]',item.name);
	  
	});

	console.log(form_data);
					
	$.ajax({
		type: 'POST',
		url: '/claim_form',
		contentType: false,
		processData: false,
		data: form_data,
		dataType: 'json',
		error:function (err){
           console.log(err);
		},
		success:function(response) {
			console.log(response);
		    if(response.errors)
			{
				Object.keys(response.errors).map((item)=>{

                    if(item==='type') $(form).find('select[name=type]').css({"border":"1px solid red"});
                    else if(item==='desc') $(form).find("textarea[name="+item+"]").css({"border":"1px solid red"});
                    else $(form).find("input[name="+item+"]").css({"border":"1px solid red"});
                    
                });
			}
			else
			{
				$('#hd').hide();
				$('.claim_form').hide();
				$('.success_block').show();
			}
		}
	});
}


/** :: enjCom ::
 ***********************************************************************************************************************
 * @source  :
 * @project :
 *----------------------------------------------------------------------------------------------------------------------
 * VER  DATE           AUTHOR          DESCRIPTION
 * ---  -------------  --------------  ---------------------------------------------------------------------------------
 * 1.0  2017/12/29     Name_0070
 * ---  -------------  --------------  ---------------------------------------------------------------------------------
 * Project Description
 * Copyright(c) 2015 enjCom Co., Ltd. All rights reserved.
 **********************************************************************************************************************/

"use strict";
//######################################################################################################################
//##
//## >> Function  :
//##
//######################################################################################################################

function removeData(id) {
    swal({
        title: '정보 삭제',
        html: '<span class="swal-message">정보 삭제 하시겠습니까?</span>',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: '삭제',
        cancelButtonText: '취소',
    }).then(function(res) {
        if (res.value) {
            $.ajax({
                url: routes.delete,
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    '_method' : 'delete'
                },
                success: function (result) {
                    if (result.code == 200) {
                        return swal({
                            title: '성공',
                            html: '<span class="swal-message">정보 삭제 되었습니다.</span>',
                            type: 'success',
                            confirmButtonText: '확인',
                        }).then(function (res) {
                            location.href = routes.detail;
                        });
                    } else {
                        return swal({
                            title: '삭제',
                            html: '<span class="swal-message">삭제에 실패했습니다.<br/>에러코드: ' + result.code + '</span>',
                            type: 'warning',
                        });
                    }
                }
            });
        }
    });
}

// 결제 취소 확인
function cancelCheck(bool = true, cancel) {
    if(bool) {
        swal({
            title: '알림',
            html: '<div><p>취소하실 금액: <span>'+number_format(cancel)+'원</span></p></div>',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '확인',
            cancelButtonText: '취소',
        }).then(function(res) {
            if(res.value){
                $("#paymentCancel").submit();
            } else {
                $('.cancel_amount').val("0");
            }
        });
    }
}

// 결제 취소
function cancelPaymentPage(bool = true, batch) {
    if(bool) {
        let batchNum = Number(batch);
        swal({
            title: '결제 취소',
            html:
                '<div class="form-group">' +
                '    <div class="radio">' +
                '        <label>' +
                '        <input type="radio" name="payment" id="batch" class="payment-radio" value="0" checked>' +
                '        일괄취소' +
                '        </label>' +
                '    </div>' +
                '    <div class="radio">' +
                '        <label>' +
                '        <input type="radio" name="payment" id="division" class="payment-radio" value="3">' +
                '        분할취소' +
                '        </label>' +
                '    </div>' +
                '</div>' +
                '<div class="form-group">' +
                '    <label for="exampleInputEmail1" class="float-left">부분 취소 금액</label>' +
                '    <input type="number" placeholder="부분 취소 금액을 입력해주세요." id="cancel" class="form-control" max="'+batchNum+'" value="'+batchNum+'">' +
                '</div>' +
                '<span class="payment-error">금액을 초과하였습니다. 최대 '+number_format(batch)+'원 까지만 환불 가능합니다.</span>',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '확인',
            cancelButtonText: '취소',
            onOpen: function(){
                $('input[name=payment]').change(function(){
                    $('input[name=payment]').each(function(){
                        let paymentType = $(this).val();
                        let checked = $('#batch').prop('checked');
                        if( checked ){
                            $('#cancel').val(batch);
                        } else {
                            $('#cancel').val("");
                        }

                        $('.payment_type').val(paymentType);
                    });
                });

                $('#cancel').on('change keyup', function(){
                    let pay = $(this).val();
                    if( Number(pay) > Number(batch) ){
                        $(this).val("");
                        $('.payment-error').addClass('active');
                    } else {
                        $('.payment-error').removeClass('active');
                    }
                });
            },
        }).then(function(res) {
            if(res.value){
                var cancel = $('#cancel').val();
                $('.cancel_amount').val(cancel);
                cancelCheck(true, cancel);
            } else {
                $('.cancel_amount').val("0");
            }
        });
    }
}

//# 공고 취소 -> 진행대기
function cancelPage(bool = true) {
    if(bool) {
        swal({
            title: '알림',
            html: '공고를 취소하시겠습니까?' +
                  '<div class="form-group">' +
                  '    <div class="radio">' +
                  '        <label>' +
                  '        <input type="radio" name="cancel" id="cancel_cgs" class="payment-radio" value="caregiver" checked>' +
                  '        간병인에 의한 취소' +
                  '        </label>' +
                  '    </div>' +
                  '    <div class="radio">' +
                  '        <label>' +
                  '        <input type="radio" name="cancel" id="cancel_ptr" class="payment-radio" value="protector">' +
                  '        보호자에 의한 취소' +
                  '        </label>' +
                  '    </div>' +
                  '</div>',
            type: 'warning',
            onOpen: function(){
                var cancel_obj = $('input[name=cancel]');
                cancel_obj.change(function(){
                    var obj = $(this);
                    $('input[name=cancel_user]').val(obj.val());
                });
            },
            showCancelButton: true,
            confirmButtonText: '확인',
            cancelButtonText: '취소',
        }).then(function(res) {
            if(res.value){
                $("#jobCancel").submit();
            }
        });
    }
}

//# 공고 취소 -> 진행중
function cancelProgressPage(bool = true, start, end) {
    if(bool) {
        swal({
            title: '알림',
            html:
                '<p>종료일자를 입력해주세요!<br/>' +
                '종료일자는 공고 시작일과 종료일 <br/>사이로만 입력이 가능합니다.<br/></p>' +
                '<div class="form-group" style="margin-bottom: 40px">' +
                '    <label for="exampleInputEmail1" class="float-left">종료 날짜</label>' +
                '    <input type="text" class="form-control pull-right" id="endDatepicker">' +
                '</div>' +
                '<div class="form-group">' +
                '    <div class="radio">' +
                '        <label>' +
                '        <input type="radio" name="cancel" id="cancel_cgs" class="payment-radio" value="caregiver" checked>' +
                '        간병인에 의한 취소' +
                '        </label>' +
                '    </div>' +
                '    <div class="radio">' +
                '        <label>' +
                '        <input type="radio" name="cancel" id="cancel_ptr" class="payment-radio" value="protector">' +
                '        보호자에 의한 취소' +
                '        </label>' +
                '    </div>' +
                '</div>',
            onOpen: function(){
                $('#endDatepicker').datetimepicker({
                    format: 'YYYY-MM-DD HH:00:00',
                });
                var cancel_obj = $('input[name=cancel]');
                cancel_obj.change(function(){
                    var obj = $(this);
                    $('input[name=cancel_user]').val(obj.val());
                });
            },
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '확인',
            cancelButtonText: '취소',
        }).then(function(res) {
            if(res.value){
                var val = $('#endDatepicker').val();
                $('input[name=job_cancel_at]').val(val);
                $("#jobCancel").submit();
            }
        });
    }
}


// 공고 업데이트
function jobAutoPage(bool = true) {
    if(bool) {
        swal({
            title: '알림',
            html: '공고정보를 수정 하시겠습니까?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '확인',
            cancelButtonText: '취소',
        }).then(function(res) {
            if(res.value){
                $("#manualCancel").submit();
            }
        });
    }
}

// 수동공고 취소
function manualCancelPage(bool = true) {
    if(bool) {
        swal({
            title: '알림',
            html: '공고를 취소 하시겠습니까?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '확인',
            cancelButtonText: '취소',
        }).then(function(res) {
            if(res.value){
                $("#manualCancel").submit();
            }
        });
    }
}

// 공고취소 철회
function withdrawalCancelPage(bool = true) {
    if(bool) {
        swal({
            title: '알림',
            html: '공고를 취소요청을 철회 하시겠습니까?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '확인',
            cancelButtonText: '취소',
        }).then(function(res) {
            if(res.value){
                $("#withdrawal").submit();
            }
        });
    }
}

function jobReset(){
    $('.form-group input').val('');
    $('.form-group button').removeClass('active');
    window.dataTable.search('');
    window.dataTable.columns(4).search('');
    window.dataTable.columns(5).search('');
    window.dataTable.columns(8).search('');
    window.dataTable.columns(9).search('');
    window.dataTable.columns(10).search('');
    window.dataTable.columns(11).search('');
    window.dataTable.columns(12).search('');
    window.dataTable.columns(13).search('');
    window.dataTable.draw();
    location.href=window.location.pathname+'?status=job';
}

function ratingCreate(bool = true){
    if(bool) {
        swal({
            title: '알림',
            html: '한줄평을 등록 하시겠습니까?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '확인',
            cancelButtonText: '취소',
        }).then(function(res) {
            if(res.value){
                $("#rating").submit();
            }
        });
    }
}

function successPop(bool = true){
    if(bool) {
        swal({
            title: '성공',
            html: '<span class="swal-message">등록 되었습니다.</span>',
            type: 'success',
            confirmButtonText: '확인',
        }).then(function (res) {
            location.href = routes.detail;
        });
    }
}

function failPop(bool = true){
    if(bool) {
        swal({
            title: '실패',
            html: '<span class="swal-message">등록 실패하였습니다.</span>',
            type: 'warning',
            confirmButtonText: '확인',
        });
    }
}

function ptrPayment(){
    var amount = $('input[name=amount]').val();
    var etc = $('textarea[name=etc]').val();
    $.ajax({
        url: routes.bankCreate,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            'amount' : amount,
            'etc' : etc
        },
        success: function(data){
            if(data.code == 200){
                successPop();
                $('.ptr_job_bank').removeClass('active');
            } else {
                successPop();
            }
        }
    });
}

function removeBankData(id) {
    swal({
        title: '정보 삭제',
        html: '<span class="swal-message">정보 삭제 하시겠습니까?</span>',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: '삭제',
        cancelButtonText: '취소',
    }).then(function(res) {
        if (res.value) {
            $.ajax({
                url: routes.bankDelete,
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    '_method' : 'delete'
                },
                data: {
                    'id' : id
                },
                success: function (result) {
                    if (result.code == 200) {
                        return swal({
                            title: '성공',
                            html: '<span class="swal-message">정보 삭제 되었습니다.</span>',
                            type: 'success',
                            confirmButtonText: '확인',
                        }).then(function (res) {
                            location.href = routes.detail;
                        });
                    } else {
                        return swal({
                            title: '삭제',
                            html: '<span class="swal-message">삭제에 실패했습니다.<br/>에러코드: ' + result.code + '</span>',
                            type: 'warning',
                        });
                    }
                }
            });
        }
    });
}

function searchJobReset(){
    location.href=window.location.pathname+'?status=job';
}

function cancelCgsAmount(bool = true, info) {
    if(bool) {
        swal({
            title: '간병비 지급',
            html:
                '<form name="jobAmount">' +
                '   <div class="form-group">' +
                '       <label for="exampleInputEmail1">제목</label>' +
                '        <textarea name="title" class="form-control" placeholder="제목을 입력하세요.">'+info+': '+'</textarea>' +
                '   </div>' +
                '   <div class="form-group">' +
                '       <label for="exampleInputEmail1">금액 <b class="text-red"> (실 지급될 금액을 입력해 주세요.)</b></label>' +
                '        <input type="text" name="amount" class="form-control" placeholder="금액을 입력하세요.">' +
                '   </div>' +
                '   <div class="form-group">' +
                '       <label for="exampleInputEmail1">아래는 정산용 정보입니다.--------------------------------------------------------------------</label>' +
                '   </div>' +
                '   <div class="form-group">' +
                '       <label for="exampleInputEmail1">보험료 <span>(첫날, 마지막날 확인필요)</span></label>' +
                '       <input type="text" name="fee_insurance" class="form-control" placeholder="보험료를 입력하세요." value="0">' +
                '   </div>' +
                '   <div class="form-group">' +
                '       <label for="exampleInputEmail1">수수료 <span></span> </label>' +
                '       <input type="text" name="fee_company" class="form-control" placeholder="수수료를 입력하세요." value="0">' +
                '   </div>' +
                '   <input type="hidden" name="cgs_users_id" value="'+amount_data.cgs_users_id+'">' +
                '   <input type="hidden" name="ptr_job_id" value="'+amount_data.ptr_job_id+'">' +
                '</form>',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '확인',
            cancelButtonText: '취소',
        }).then(function(res) {
            if(res.value){
                const dataFormInput = $('form[name=jobAmount]').serialize();
                $.ajax({
                    url: routes.jobAmount,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    data: dataFormInput,
                    success: function (result) {
                        if(result.code == 200){
                            successPop();
                        } else {
                            failPop();
                        }
                    }
                })
            }
        });
    }
}


function batchJobCancel(jobInfo, paymentIds, csrf) {
    //# 결제정보
    var cancelInputStr = "";
    var maxPayment = 0;
    for(var idx in paymentIds) {
        var pay = paymentIds[idx];
        maxPayment += pay['amount'];
        cancelInputStr += '<label class="col-lg-12" for="exampleInputEmail1" style="text-align: center !important;">['+jobInfo['ptrUsers']['name']+'] 보호자 결제 취소 (취소가능최대 금액 : '+number_format(pay['amount'])+')</label>';
        cancelInputStr += '<div class="col-lg-12">';
        cancelInputStr += '     <div class="col-lg-3" style="padding-left: 0px !important; padding-right: 1px !important; width:23%">';
        cancelInputStr += '           <label>';
        cancelInputStr += '           <div class="input-group">';
        cancelInputStr += '               <span class="input-group-addon">';
        cancelInputStr += '                <input type="radio" name="paymentType_'+pay['id']+'" id="division" data-id ="'+pay['id']+'" data-amount="" class="payment-radio" value="part" checked>';
        cancelInputStr += '               </span>';
        cancelInputStr += '               <input type="text" class="form-control" value="부분" disabled>';
        cancelInputStr += '           </div>';
        cancelInputStr += '           </label>';
        cancelInputStr += '     </div>';
        cancelInputStr += '     <div class="col-lg-6" style="padding-left: 0px !important; !important; width:75%">';
        cancelInputStr += '         <input type="number" placeholder="부분 취소 금액을 입력해주세요." name="paymentId_'+pay['id']+'" data-max="'+pay['amount']+'" class="form-control text-right" data-payment-id="'+pay['id']+'">';
        cancelInputStr += '     </div>';
        cancelInputStr += '     <div class="col-lg-12" style="padding-left: 0px !important; padding-right: 1px !important; ">';
        cancelInputStr += '           <label>';
        cancelInputStr += '           <div class="input-group">';
        cancelInputStr += '               <span class="input-group-addon">';
        cancelInputStr += '                   <input type="radio" name="paymentType_'+pay['id']+'" id="batch" data-id ="'+pay['id']+'" data-amount="'+pay['amount']+'" class="payment-radio" value="all">';
        cancelInputStr += '               </span>';
        cancelInputStr += '               <input type="text" class="form-control" value="전액" disabled>';
        cancelInputStr += '           </div>';
        cancelInputStr += '           </label>';
        cancelInputStr += '     </div>';
        cancelInputStr += '</div>';
        cancelInputStr += '<span class="col-lg-12 payment-error payment-'+pay['id']+'">금액을 초과하였습니다. 최대 '+number_format(pay['amount'])+'원 까지만 환불 가능합니다.</span>';
    }


    var selectPenaltyStr = "";

    selectPenaltyStr +=


    swal({
        title: '결제 취소 및 공고 종료 처리',
        html:
            '<p style="text-align: right">' +
            '   공고 시작 시간 : <b class="text-green">'+jobInfo['job_start_date']+'</b> <br/>' +
            '   공고 종료 시간 : <b class="text-blue">'+jobInfo['job_end_date']+'</b> <br/>' +
            '   총 결제 금액 : <b class="text-red">'+number_format(maxPayment)+'원</b> <br/>' +
            '   종료날짜는 공고 시작/종료일 사이로만 입력이 가능합니다.<br/>' +
            '</p>' +
            '<hr>'+
            '<form name="jobCancelForm" method="post" action="'+routes.oneClickCancel+'">'+
            '<input type="hidden" name="_token" value="'+csrf+'" />'+
            '<input type="hidden" name="ptrJobId" value="'+jobInfo['id']+'" >'+
            '<input type="hidden" name="ptrUserPhone" value="'+jobInfo['ptrUsers']['phone']+'" >'+
            '<div class="form-group" style="margin-bottom: 0">' +
            '   <div class="row">' +
            '       <div class="col-lg-6">' +
            '           <label>' +
            '           <div class="input-group">' +
            '               <span class="input-group-addon">' +
            '                   <input type="radio" name="cancel" id="cancel_cgs" class="payment-radio" value="caregiver" checked>' +
            '               </span>' +
            '               <input type="text" class="form-control" value="간병인에 의한 취소" disabled>' +
            '           </div>' +
            '           </label>' +
            '       </div>' +
            '       <div class="col-lg-6">' +
            '           <label>' +
            '           <div class="input-group">' +
            '               <span class="input-group-addon">' +
            '                   <input type="radio" name="cancel" id="cancel_ptr" class="payment-radio" value="protector">' +
            '               </span>' +
            '               <input type="text" class="form-control" value="보호자에 의한 취소" disabled>' +
            '           </div>' +
            '           </label>' +
            '       </div>' +
            '   </div>'+
            '</div>'+
            '<input type="text" class="form-control  pull-right text-right" id="endDatepicker" name="cancel_at" placeholder="종료날짜를 입력해주세요" required>' +
            '<hr style="margin-top: 0">'+
            cancelInputStr +
            '   </div>'+
            '</div>'+
            '</input>'
        ,onOpen: function(){
            $('#endDatepicker').datetimepicker({
                format: 'YYYY-MM-DD HH:00:00',
            });

            var cancel_obj = $('input[name=cancel]');
            cancel_obj.change(function(){
                var obj = $(this);
                $('input[name=cancel_user]').val(obj.val());
            });

            //# 결제 취소 방식 선택
            $('input[type=radio]').change(function(){
                var payId       = $(this).attr('data-id');
                var payAmount   = $(this).attr('data-amount');
                var val         = $(this).val();
                var objKeyName  = "paymentId_"+payId;
                var obj         = $('input[name='+objKeyName+']');
                obj.val(payAmount).prop('readonly', (val == 'all'));

                // 부분 / 취소에 따른 위약금 호출
                if(val == 'protector' && payAmount == 'all'){
                    console.log(val);

                }

            });

            //# 결제 최대금액 체크
            $('input[type=number]').on('change keyup', function(){
                let pay = $(this).val();
                let max = $(this).attr('data-max');
                if( (pay * 1) > (max * 1) ){
                    $(this).val("");
                    var className = '.payment-'+$(this).attr('data-payment-id')
                    $(className).addClass('active');
                } else {
                    var className = '.payment-'+$(this).attr('data-payment-id')
                    $(className).removeClass('active');
                }
            });


        },

        showCancelButton: true,
        confirmButtonText: '확인',
        cancelButtonText: '취소',
    }).then(function(res) {

        if (res.value) {
            var amount = 0;
            $('input[type=number]').each(function() {
                var pay = $(this).val();
                amount += (pay * 1);
            })

            if($("#endDatepicker").val() == "") {
                swal({
                    title: '일자',
                    html: '<span class="swal-message">공고종료일 입력해주세요.</span>',
                    type: 'warning',
                    showCancelButton: false,
                    confirmButtonText: '확인',
                });
            }else if(amount <= 0) {
                swal({
                    title: '금액',
                    html: '<span class="swal-message">취소금액을 입력해주세요.</span>',
                    type: 'warning',
                    showCancelButton: false,
                    confirmButtonText: '확인',
                });
            } else {
                //# 총 결제금액과 취소금액이 동일할 경우 정보 변경
                if(maxPayment == amount) {
                    $('input[value="all"]').prop('checked', true);
                    $('#endDatepicker').val(jobInfo['job_start_date'])
                }

                $('form[name=jobCancelForm]').submit();
            }
        }




        // if (res.value) {
        //     const dataFormInput = $('form[name=jobCancelForm]').serialize();
        //     console.log(dataFormInput);
        //     $.ajax({
        //         url: routes.oneClickCancel,
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        //         },
        //         data: dataFormInput,
        //         success: function (result) {
        //             if(result.code == 200){
        //                 successPop();
        //             } else {
        //                 failPop();
        //             }
        //         }
        //     })
        // }
    });
}


function oneClickJobCancel(jobInfo, paymentIds, csrf) {

    //# 결제정보
    var cancelInputStr = "";
    var maxPayment = 0;
    for(var idx in paymentIds) {
        var pay = paymentIds[idx];
        maxPayment += pay['amount'];
        cancelInputStr += '<label class="col-lg-12" for="exampleInputEmail1" style="text-align: center !important;">['+pay['ptr_user']['name']+'] 보호자 결제 취소 (취소가능최대 금액 : '+number_format(pay['amount'])+')</label>';
        cancelInputStr += '<div class="col-lg-12">';
        cancelInputStr += '<div class="col-lg-3" style="padding-left: 0px !important; padding-right: 1px !important; width:23%">';
        cancelInputStr += '           <label>';
        cancelInputStr += '           <div class="input-group">';
        cancelInputStr += '               <span class="input-group-addon">';
        cancelInputStr += '                <input type="radio" name="paymentType_'+pay['id']+'" id="division" data-id ="'+pay['id']+'" data-amount="" class="payment-radio" value="part" checked>';
        cancelInputStr += '               </span>';
        cancelInputStr += '               <input type="text" class="form-control" value="부분" disabled>';
        cancelInputStr += '           </div>';
        cancelInputStr += '           </label>';
        cancelInputStr += '       </div>';
        cancelInputStr += '<div class="col-lg-3" style="padding-left: 0px !important; padding-right: 1px !important; width:23%">';
        cancelInputStr += '           <label>';
        cancelInputStr += '           <div class="input-group">';
        cancelInputStr += '               <span class="input-group-addon">';
        cancelInputStr += '                   <input type="radio" name="paymentType_'+pay['id']+'" id="batch" data-id ="'+pay['id']+'" data-amount="'+pay['amount']+'" class="payment-radio" value="all">';
        cancelInputStr += '               </span>';
        cancelInputStr += '               <input type="text" class="form-control" value="전액" disabled>';
        cancelInputStr += '           </div>';
        cancelInputStr += '           </label>';
        cancelInputStr += '       </div>' ;
        cancelInputStr += '<div class="col-lg-6" style="padding-left: 0px !important; !important; width:53%">';
        cancelInputStr += '<input type="number" placeholder="부분 취소 금액을 입력해주세요." name="paymentId_'+pay['id']+'" data-max="'+pay['amount']+'" class="form-control text-right" data-payment-id="'+pay['id']+'">';
        cancelInputStr += '</div>';
        cancelInputStr += '</div>';
        cancelInputStr += '<span class="col-lg-12 payment-error payment-'+pay['id']+'">금액을 초과하였습니다. 최대 '+number_format(pay['amount'])+'원 까지만 환불 가능합니다.</span>';
    }

    swal({
        title: '결제 취소 및 공고 종료 처리',
        html:
            '<p style="text-align: right">' +
            '   공고 시작 시간 : <b class="text-green">'+jobInfo['job_start_date']+'</b> <br/>' +
            '   공고 종료 시간 : <b class="text-blue">'+jobInfo['job_end_date']+'</b> <br/>' +
            '   총 결제 금액 : <b class="text-red">'+number_format(maxPayment)+'원</b> <br/>' +
            '   종료날짜는 공고 시작/종료일 사이로만 입력이 가능합니다.<br/>' +
            '</p>' +
            '<hr>'+
            '<form name="jobCancelForm" method="post" action="'+routes.oneClickCancel+'">'+
            '<input type="hidden" name="_token" value="'+csrf+'" />'+
            '<input type="hidden" name="ptrJobId" value="'+jobInfo['id']+'" >'+
            '<input type="hidden" name="ptrUserPhone" value="'+jobInfo['ptrUsers']['phone']+'" >'+
            '<div class="form-group" style="margin-bottom: 0">' +
            '   <div class="row">' +
            '       <div class="col-lg-6">' +
            '           <label>' +
            '           <div class="input-group">' +
            '               <span class="input-group-addon">' +
            '                   <input type="radio" name="cancel" id="cancel_cgs" class="payment-radio" value="caregiver" checked>' +
            '               </span>' +
            '               <input type="text" class="form-control" value="간병인에 의한 취소" disabled>' +
            '           </div>' +
            '           </label>' +
            '       </div>' +
            '       <div class="col-lg-6">' +
            '           <label>' +
            '           <div class="input-group">' +
            '               <span class="input-group-addon">' +
            '                   <input type="radio" name="cancel" id="cancel_ptr" class="payment-radio" value="protector">' +
            '               </span>' +
            '               <input type="text" class="form-control" value="보호자에 의한 취소" disabled>' +
            '           </div>' +
            '           </label>' +
            '       </div>' +
            '   </div>'+
            '</div>'+
            '<input type="text" class="form-control  pull-right text-right" id="endDatepicker" name="cancel_at" placeholder="종료날짜를 입력해주세요" required>' +
            '<hr style="margin-top: 0">'+
            cancelInputStr +
            '   </div>'+
            '</div>'+
            '</input>'
        ,onOpen: function(){
            $('#endDatepicker').datetimepicker({
                format: 'YYYY-MM-DD HH:00:00',
            });

            var cancel_obj = $('input[name=cancel]');
            cancel_obj.change(function(){
                var obj = $(this);
                $('input[name=cancel_user]').val(obj.val());
            });

            //# 결제 취소 방식 선택
            $('input[type=radio]').change(function(){
                var payId       = $(this).attr('data-id');
                var payAmount   = $(this).attr('data-amount');
                var val         = $(this).val();
                var objKeyName  = "paymentId_"+payId;
                var obj         = $('input[name='+objKeyName+']');
                obj.val(payAmount).prop('readonly', (val == 'all'));
            });

            //# 결제 최대금액 체크
            $('input[type=number]').on('change keyup', function(){
                let pay = $(this).val();
                let max = $(this).attr('data-max');
                if( (pay * 1) > (max * 1) ){
                    $(this).val("");
                    var className = '.payment-'+$(this).attr('data-payment-id')
                    $(className).addClass('active');
                } else {
                    var className = '.payment-'+$(this).attr('data-payment-id')
                    $(className).removeClass('active');
                }
            });

        },

        showCancelButton: true,
        confirmButtonText: '확인',
        cancelButtonText: '취소',
    }).then(function(res) {

        if (res.value) {
            var amount = 0;
            $('input[type=number]').each(function() {
                var pay = $(this).val();
                amount += (pay * 1);
            })

            if($("#endDatepicker").val() == "") {
                swal({
                    title: '일자',
                    html: '<span class="swal-message">공고종료일 입력해주세요.</span>',
                    type: 'warning',
                    showCancelButton: false,
                    confirmButtonText: '확인',
                });
            }else if(amount <= 0) {
                swal({
                    title: '금액',
                    html: '<span class="swal-message">취소금액을 입력해주세요.</span>',
                    type: 'warning',
                    showCancelButton: false,
                    confirmButtonText: '확인',
                });
            } else {
                //# 총 결제금액과 취소금액이 동일할 경우 정보 변경
                if(maxPayment == amount) {
                    $('input[value="all"]').prop('checked', true);
                    $('#endDatepicker').val(jobInfo['job_start_date'])
                }

                $('form[name=jobCancelForm]').submit();
            }
        }




        // if (res.value) {
        //     const dataFormInput = $('form[name=jobCancelForm]').serialize();
        //     console.log(dataFormInput);
        //     $.ajax({
        //         url: routes.oneClickCancel,
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        //         },
        //         data: dataFormInput,
        //         success: function (result) {
        //             if(result.code == 200){
        //                 successPop();
        //             } else {
        //                 failPop();
        //             }
        //         }
        //     })
        // }
    });
}

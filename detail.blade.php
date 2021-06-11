@extends('layouts.admin.frame')

@section('content')
    @php( $session = Auth::guard('admin')->user() )
    @include('layouts.admin.message')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="tab-wrap">
                    <div class="tab-item active">공고 정보</div>
                    <div class="tab-item">환자 정보</div>
                    <div class="tab-item">지급 정보</div>
                    <div class="tab-item">간병인 지원 정보</div>
                    <div class="tab-item">결제 상태</div>
                    <div class="tab-item">지급 상태</div>
                    <div class="tab-item">공고 근처 간병인</div>
                    <div class="tab-item">간병인 한줄평</div>
                    <div class="tab-item">취소내역</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 tab-content active" data-target="0">
                @include('layouts.admin.error_message')
                <form action="{{ route('ptrjob.manualCancel', ['id' => $id]) }}" id="manualCancel" method="post">
                    @csrf
                    <input type="hidden" name="cancel_type" value="">
                    <div class="box box-primary">
                        <div class="box-body">
                            <h5>간병유형</h5>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                <input type="text" name="" value="{{$info->job_type_str}}" class="form-control" readonly>
                            </div>
                            <h5>간병위치</h5>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                <input type="text" name="address" value="{{ $info->address.' '.(($info->request_type == 'hospital') ? $info->info : '').' '.$info->info_detail }}" class="form-control" readonly>
                            </div>
                            <h5>보호자 이름</h5>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                <input type="text" name="ptrName" value="{{ $info['ptrUsers']->name }}" class="form-control" readonly>
                            </div>
                            <h5>보호자 휴대전화</h5>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                <input type="text" name="ptrPhone" value="{{ $info['ptrUsers']->phone }}" class="form-control" readonly>
                            </div>
                            <h5>간병 시작일</h5>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                <input type="text" name="jobStartDate" value="{{ $info->job_start_date }}" class="form-control" readonly>
                            </div>
                            <h5>간병 종료일</h5>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                <input type="text" name="jobEndDate" value="{{ $info->job_end_date.' '.'('.getDiffDate($info->job_start_date, $info->job_end_date)['day'].'일'.' '.getDiffDate($info->job_start_date, $info->job_end_date)['hours'].'시간)' }}" class="form-control" readonly>
                            </div>
                            <h5>공고 등록일</h5>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                <input type="text" name="created_at" value="{{ $info->created_at }}" class="form-control" readonly>
                            </div>
                            <h5>진행상태 {{ $info->deleted_at }}</h5>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                <input type="text" name="status" value="{{ getJobStatusCase($info) ?? "공고 삭제" }}" class="form-control" readonly>
                            </div>
                            <h5>매칭상태</h5>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                <select name="is_matching" class="form-control">
                                    <option value="auto" {{ $info->is_matching == 'auto' ? 'selected' : '' }}>자동</option>
                                    <option value="manual" {{ $info->is_matching == 'manual' ? 'selected' : '' }}>수동</option>
                                </select>
                            </div>
                            @if( $info->cancel_status == 'R' || $info->cancel_status == 'Y' || !empty($info->deleted_at) )
                                <h5>취소 사유</h5>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                    <input type="text" name="deleted_type" value="{{ getJobDeletedType($info->deleted_type) ?? '-' }}" class="form-control" readonly>

                                </div>
                            @endif
                        </div>
                        <div class="box-footer text-right">
                            @if( $info->cancel_status == 'N' )
                                @if( count($paymentList) > 0 && getAdminUser()->name === '윤동혁' )
                                    <button type="button" class="btn btn-default" onclick="paymentCashCancel();">현금결제 취소</button>
                                    <button type="button" class="btn btn-success" onclick="paymentCash();">현금결제</button>
                                @endif

                                @if($info->status == 3 || $info->status == 4)
                                    <button type="button" class="btn btn-danger btn-job" data-value="cancel" onclick="manualCancelPage()">수동공고 취소</button>
                                @endif
                                <button type="button" class="btn btn-warning btn-job" data-value="close" onclick="manualCancelPage()">수동공고 마감</button>
                                <button type="button" class="btn btn-primary btn-job" data-value="update" onclick="jobAutoPage()">수정</button>
                            @endif
                        </div>
                        {{-- 결제취소 관리 --}}
                        <div class="basic-wrap">
                            @if( in_array($info->status, [3,4,5])
                                && (
                                    (in_array(Auth::guard('admin')->user()->division_code, ['CS','AD','DV'])))
                                )
{{--                                <button type="button" class="btn btn-danger" onclick="oneClickJobCancel( {{$info}}, {{$paymentList}}, '{{ csrf_token() }}' )">일괄 취소 기능</button>--}}
                                <button type="button" class="btn btn-danger" onclick="batchJobCancel( {{$info}}, {{$paymentList}}, '{{ csrf_token() }}' )">일괄 취소 기능</button>

                            @endif
                        </div>
                    </div>
                </form>
                @if($info->cancel_status == 'R' && in_array($session->division_code, ['AD', 'CS', 'DV']))
                    {!! Form::open(['id' => 'jobCancel', 'method' => 'post', 'class' => 'inline']) !!}
                    @csrf
                    {{ method_field ('POST') }}
                    <input type="hidden" name="job_cancel_at">
                    <input type="hidden" name="cancel_user" value="caregiver">
                    <input type="hidden" name="job_status" value="{{ $info->status }}">
                    <button type="button" class="btn btn-danger" onclick="{{ $info->status == 4 ? 'cancelProgressPage()' : 'cancelPage()' }}">공고취소 승인</button>
                    {!! Form::close() !!}
                @endif
                @if(($info->cancel_status == 'R' || $info->cancel_status == 'Y') && in_array($session->division_code, ['AD', 'CS', 'DV']))
                    <form action="{{ route('ptrjob.withdrawal', ['id' => $id]) }}" id="withdrawal" class="inline" method="post">
                        @csrf
                        <button type="button" class="btn btn-warning" onclick="withdrawalCancelPage()">
                            {{ $info->cancel_status == 'R' ? '공고취소 요청 철회' : '공고취소 철회'  }}
                        </button>
                    </form>
                @endif
            </div>
            <div class="col-xs-12 tab-content" data-target="1">
                <div class="box box-primary">
                    <div class="box-body">
                        <h5>환자 이름</h5>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-building"></i></span>
                            <input type="text" name="patientName" value="{{ $info->patient_name }}" class="form-control" readonly>
                        </div>
                        <h5>성별</h5>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-building"></i></span>
                            <div class="inputItem-wrap checkbox-wrap">
                                <input type="radio" name="patientGender" value="1" {{ $info->patient_gender < 2 ? "checked" : "" }} disabled>
                                <label for="patientGender">남자</label>
                            </div>
                            <div class="inputItem-wrap checkbox-wrap">
                                <input type="radio" name="patientGender" value="2" {{ $info->patient_gender > 1 ? "checked" : "" }} disabled>
                                <label for="patientGender">여자</label>
                            </div>
                        </div>
                        <h5>환자 몸무게</h5>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-building"></i></span>
                            <input type="text" name="patientWeight" value="{{ $info->patient_weight }}" class="form-control" readonly>
                        </div>
                        <h5>환자 나이</h5>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-building"></i></span>
                            <input type="text" name="patientAge" value="{{ $info->patient_age }}" class="form-control" readonly>
                        </div>
                        <h5>환자 상태</h5>
                        <table class="patient-status">
                            <colgroup>
                                <col width="10%">
                                <col width="40%">
                                <col width="10%">
                                <col width="40%">
                            </colgroup>
                            <tr>
                                @if( $info->request_type == 'hospital' )
                                    <th>병실분류</th>
                                    <td>
                                        <div class="check-wrap">
                                            <input type="radio" name="sickroom" value="1" {{ $info->ability_sickroom_type == 1 ? "checked" : "" }} disabled>
                                            <label for="sickroom">일반환자</label>
                                        </div>
                                        <div class="check-wrap">
                                            <input type="radio" name="sickroom" value="2" {{ $info->ability_sickroom_type == 2 ? "checked" : "" }} disabled>
                                            <label for="sickroom">중환자실</label>
                                        </div>
                                        <div class="check-wrap">
                                            <input type="radio" name="sickroom" value="3" {{ $info->ability_sickroom_type == 3 ? "checked" : "" }} disabled>
                                            <label for="sickroom">응급실</label>
                                        </div>
                                        <div class="check-wrap">
                                            <input type="radio" name="sickroom" value="4" {{ $info->ability_sickroom_type == 4 ? "checked" : "" }} disabled>
                                            <label for="sickroom">격리실(VRE,CRE 등)</label>
                                        </div>
                                        <div class="check-wrap">
                                            <input type="radio" name="sickroom" value="5" {{ $info->ability_sickroom_type == 5 ? "checked" : "" }} disabled>
                                            <label for="sickroom">폐쇄병실</label>
                                        </div>
                                    </td>
                                @else
                                    <th>집</th>
                                    <td></td>
                                @endif
                                <th>석션</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="suction" value="1" {{ $info->ability_suction == 1 ? "checked" : "" }} disabled>
                                        <label for="suction">있음</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="suction" value="2" {{ $info->ability_suction == 2 ? "checked" : "" }} disabled>
                                        <label for="suction">없음</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>마비상태</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="paralysis" value="1" {{ $info->ability_paralysis == 1 ? "checked" : "" }} disabled>
                                        <label for="paralysis">전신마비</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="paralysis" value="2" {{ $info->ability_paralysis == 2 ? "checked" : "" }} disabled>
                                        <label for="paralysis">편마비</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="paralysis" value="3" {{ $info->ability_paralysis == 3 ? "checked" : "" }} disabled>
                                        <label for="paralysis">없음</label>
                                    </div>
                                </td>
                                <th>재활</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="rehabilitate" value="1" {{ $info->ability_rehabilitate == 1 ? "checked" : "" }} disabled>
                                        <label for="rehabilitate">있음</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="rehabilitate" value="2" {{ $info->ability_rehabilitate == 2 ? "checked" : "" }} disabled>
                                        <label for="rehabilitate">없음</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>의식상태</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="consciousness" value="1" {{ $info->ability_consciousness == 1 ? "checked" : "" }} disabled>
                                        <label for="consciousness">의식있음</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="consciousness" value="2" {{ $info->ability_consciousness == 2 ? "checked" : "" }} disabled>
                                        <label for="consciousness">의식없음</label>
                                    </div>
                                </td>
                                <th>투석</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="dialysis" value="1" {{ $info->ability_dialysis == 1 ? "checked" : "" }} disabled>
                                        <label for="dialysis">있음</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="dialysis" value="2" {{ $info->ability_dialysis == 2 ? "checked" : "" }} disabled>
                                        <label for="dialysis">없음</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>인지상태</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="checkbox" name="cognitive" value="1" {{ ($info->ability_cognitive & 1) > 0 ? "checked" : "" }} disabled>
                                        <label for="cognitive">치매</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="checkbox" name="cognitive" value="2" {{ ($info->ability_cognitive & 2) > 0 ? "checked" : "" }} disabled>
                                        <label for="cognitive">섬망</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="checkbox" name="cognitive" value="3" {{ $info->ability_cognitive == 0 ? "checked" : "" }} disabled>
                                        <label for="cognitive">없음</label>
                                    </div>
                                </td>
                                <th>욕창</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="change_posture" value="1" {{ $info->ability_change_posture == 1 ? "checked" : "" }} disabled>
                                        <label for="change_posture">있음</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="change_posture" value="2" {{ $info->ability_change_posture == 2 ? "checked" : "" }} disabled>
                                        <label for="change_posture">없음</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>화장실이동</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="move_toilet" value="1" {{ $info->ability_move_toilet == 1 ? "checked" : "" }} disabled>
                                        <label for="move_toilet">스스로 가능</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="move_toilet" value="2" {{ $info->ability_move_toilet == 2 ? "checked" : "" }} disabled>
                                        <label for="move_toilet">부축필요</label>
                                    </div>
                                </td>
                                <th>식사</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="eat" value="1" {{ $info->ability_eat == 1 ? "checked" : "" }} disabled>
                                        <label for="eat">스스로 가능</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="eat" value="2" {{ $info->ability_eat == 2 ? "checked" : "" }} disabled>
                                        <label for="eat">도움필요</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>배뇨/배변</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="checkbox" name="toilet" value="1" {{ ($info->ability_toilet & 1) > 0 ? "checked" : "" }} disabled>
                                        <label for="toilet">기저귀</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="checkbox" name="toilet" value="2" {{ ($info->ability_toilet & 2) > 0 ? "checked" : "" }} disabled>
                                        <label for="toilet">소변줄</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="checkbox" name="toilet" value="3" {{ $info->ability_toilet == 0 ? "checked" : "" }} disabled>
                                        <label for="toilet">없음</label>
                                    </div>
                                </td>
                                <th>피딩</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="feeding" value="1" {{ $info->ability_feeding == 1 ? "checked" : "" }} disabled>
                                        <label for="feeding">사용중</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="feeding" value="2" {{ $info->ability_feeding == 2 ? "checked" : "" }} disabled>
                                        <label for="feeding">사용 안함</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>장루설치 유/무</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="stoma" value="1" {{ $info->ability_stoma == 1 ? "checked" : "" }} disabled>
                                        <label for="stoma">설치함</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="stoma" value="2" {{ $info->ability_stoma == 2 ? "checked" : "" }} disabled>
                                        <label for="stoma">설치안함</label>
                                    </div>
                                </td>
                                <th>거동</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="move" value="1" {{ $info->ability_move == 1 ? "checked" : "" }} disabled>
                                        <label for="move">스스로 가능</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="move" value="2" {{ $info->ability_move == 2 ? "checked" : "" }} disabled>
                                        <label for="move">부축필요</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="move" value="3" {{ $info->ability_move == 3 ? "checked" : "" }} disabled>
                                        <label for="move">불가능</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>수면장애</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="somnipathy" value="1" {{ $info->ability_somnipathy == 1 ? "checked" : "" }} disabled>
                                        <label for="somnipathy">있음</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="somnipathy" value="2" {{ $info->ability_somnipathy == 2 ? "checked" : "" }} disabled>
                                        <label for="somnipathy">없음</label>
                                    </div>
                                </td>
                                <th>우대하는 간병인 성별</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="favorite_gender" value="1" {{ $info->favorite_gender == 1 ? "checked" : "" }} disabled>
                                        <label for="favorite_gender">남자</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="favorite_gender" value="2" {{ $info->favorite_gender == 2 ? "checked" : "" }} disabled>
                                        <label for="favorite_gender">여자</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="favorite_gender" value="3" {{ $info->favorite_gender == 3 ? "checked" : "" }} disabled>
                                        <label for="favorite_gender">상관없음</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>감염질환</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="checkbox" name="ability_infectious_disease" value="1" {{ ($info->ability_infectious_disease & 1) > 0 ? "checked" : "" }} disabled>
                                        <label for="toilet">VRE</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="checkbox" name="ability_infectious_disease" value="2" {{ ($info->ability_infectious_disease & 2) > 0 ? "checked" : "" }} disabled>
                                        <label for="toilet">CRE</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="checkbox" name="ability_infectious_disease" value="4" {{ ($info->ability_infectious_disease & 4) > 0 ? "checked" : "" }} disabled>
                                        <label for="toilet">결핵</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="checkbox" name="ability_infectious_disease" value="8" {{ ($info->ability_infectious_disease & 8) > 0 ? "checked" : "" }} disabled>
                                        <label for="toilet">옴</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="checkbox" name="ability_infectious_disease" value="16" {{ ($info->ability_infectious_disease & 16) > 0 ? "checked" : "" }} disabled>
                                        <label for="toilet">독감</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="checkbox" name="ability_infectious_disease" value="32" {{ ($info->ability_infectious_disease & 32) > 0 ? "checked" : "" }} disabled>
                                        <label for="toilet">기타</label>
                                    </div>
                                </td>
                                <th>코로나 검사 필요여부</th>
                                <td>
                                    <div class="check-wrap">
                                        <input type="radio" name="ability_corona" value="1" {{ $info->ability_corona == 1 ? "checked" : "" }} disabled>
                                        <label for="">네</label>
                                    </div>
                                    <div class="check-wrap">
                                        <input type="radio" name="ability_corona" value="2" {{ $info->ability_corona == 2 ? "checked" : "" }} disabled>
                                        <label for="">아니오</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>기타</th>
                                <td><textarea type="text" class="input-style-none" disabled>{{ ($info->ability_infectious_disease & 32) ? $info->infectious_disease_etc : '' }}</textarea></td>
                                <th>-</th>
                                <td>-</td>
                            </tr>
                        </table>
                        <h5>{{ $info->job_type == 'time' ? '시급' : '일급' }} 간병비</h5>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-building"></i></span>
                            <input type="text" name="amountDay" value="{{  number_format(($info->job_type == 'time') ? $info->amount_time : $info->amount_day) }}" class="form-control" readonly>
                        </div>
                        <h5>총 간병비</h5>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-building"></i></span>
                            <input type="text" name="total" value="{{ number_format($info->total) }}" class="form-control" readonly>
                        </div>
                        <h5>환자 증상</h5>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-building"></i></span>
                            <textarea class="form-control" readonly>{{ $info->reason }}</textarea>
                        </div>
                        <h5>기타 요청사항</h5>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-building"></i></span>
                            <input type="text" name="other" value="{{ $info->other }}" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 tab-content" data-target="2">
                @if(count($bank) > 0)
                    <div class="box box-primary">
                        <div class="box-body">
                            <table class="custom-table">
                                <tr>
                                    <th>금액</th>
                                    <th>기타</th>
                                    <th>등록일</th>
                                    <th></th>
                                </tr>
                                @foreach($bank as $item)
                                    <tr>
                                        <td>{{ number_format($item->amount) }}</td>
                                        <td>{{ $item->etc }}</td>
                                        <td>{{ $item->created_at }}</td>
                                        <td>
                                            <span class="label-custom label-custom-danger cursor-pointer" onclick="removeBankData({{ $item->id }})">삭제하기</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="box-footer text-right">
                            <button type="button" class="btn btn-success btn_bank" onclick="">금액지급 작성</button>
                        </div>
                    </div>
                @else
                    <div style='text-align: center; padding-top: 30px; padding-bottom: 30px; background: #fff'>조회된 데이터가 없습니다.</div>
                    <div class="box-footer text-right">
                        <button type="button" class="btn btn-success btn_bank" onclick="">금액지급 작성</button>
                    </div>
                @endif
            </div>
            <div class="col-xs-12 tab-content" data-target="3">
                @if(count($info['applicant']) > 0 )
                    <div class="box box-primary custom">
                        <div class="box-body chart-responsive" style="">
                            <div class="basic-table-wrap">
                                <table id="cgsUsers" class="custom-table">
                                    <tr>
                                        <col width="10%">
                                        <col width="15%">
                                        <col width="5%">
                                        <col width="5%">
                                        <col width="5%">
                                        <col width="10%">
                                        <col width="10%">
                                        <col width="10%">
                                        <col width="5%">
                                        <col width="15%">
                                    </tr>
                                    <tr>
                                        <th>No</th>
                                        <th>아이디</th>
                                        <th>이름</th>
                                        <th class="text-center">성별</th>
                                        <th class="text-center">나이</th>
                                        <th>휴대전화</th>
                                        <th>{{ ($info->job_type == 'time') ? '시급' : '일급' }} 간병비</th>
                                        <th>총 간병비</th>
                                        <th class="text-center">지원 상태</th>
                                        <th class="text-center">회원 탈퇴 여부</th>
                                        <th class="text-center">지원 시간</th>
                                    </tr>
                                    @foreach($info['applicant'] as $applicant)
                                        <tr class=
                                            @switch($applicant->applicant_status)
                                            @case('선택') "choice" @break
                                        @case('지원') "" @break
                                        @default "cancel" @break
                                        @endswitch
                                        >
                                        <td><a href="/caregiver/{{ $applicant['cgs_user']->id ?? '' }}" target="_blank">{{ $applicant['cgs_user']->id ?? '' }}</a></td>
                                        <td>{{ $applicant['cgs_user']->email ?? '' }}</td>
                                        <td><a href="{{route('caregiver.detail', ['id'=>$applicant['cgs_user']->id])}}" target="_blank">{{ $applicant['cgs_user']->name ?? '' }}</a></td>
                                        <td class="text-center">{{ convert_gender($applicant['cgs_user']->gender) ?? '' }}</td>
                                        <td class="text-center">{{ convert_age($applicant['cgs_user']->birthdate) ?? '' }}</td>
                                        <td>{{ $applicant['cgs_user']->phone ?? '' }}</td>
                                        <td>{{ number_format( ($info->job_type == 'time') ? $applicant->amount_time : $applicant->amount_day) }}</td>
                                        <td>{{ number_format($applicant->total) }}</td>
                                        <td class="text-center">{{ $applicant->applicant_status ?? ''}}</td>
                                        <td class="text-center">{{ $applicant->is_deleted ?? ''}}</td>
                                        <td>{{ $applicant->created_at}}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div style='text-align: center; padding-top: 30px; padding-bottom: 30px; background: #fff'>조회된 데이터가 없습니다.</div>
                @endif
            </div>
            <div class="col-xs-12 tab-content" data-target="4">
                @if(count($paymentList) > 0)
                    @foreach($paymentList as $key=>$val)
                        <div class="box box-primary custom">
                            <div class="box-header">
                                <h3 class="box-title">결제 상태</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body chart-responsive" style="">
                                <table class="custom-table">
                                    <tr>
                                        <th>그룹이름</th>
                                        <th>보호자이름</th>
                                        <th>결제상태</th>
                                        <th>결제금액</th>
                                        <th>전체금액</th>
                                        @if(($info->cancel_status == 'R' || ($info->cancel_status == 'N' && $info->status == 5)) && in_array($session->division_code, ['AD', 'CS']))
                                            <th class="center">결제취소</th>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td>{{ $val['ptr_group']->name ?? '-' }}</td>
                                        <td>{{ $val['ptr_user']->name ?? '' }}</td>
                                        <td>{{ getAmountStatus($val->status) }}</td>
                                        <td>{{ number_format($val->amount) }}</td>
                                        <td>{{ number_format($val->total) }}</td>
                                        @if(($info->cancel_status == 'R' || ($info->cancel_status == 'N' && $info->status == 5)) && in_array($session->division_code, ['AD', 'CS']))
                                            <td>
                                                {!! Form::open(['id' => 'paymentCancel', 'method' => 'post']) !!}
                                                @csrf
                                                {{ method_field ('PUT') }}
                                                <input type="hidden" name="cancel_amount" value="0" class="cancel_amount">
                                                <input type="hidden" name="payment_id" value="{{$val->id}}">
                                                <input type="hidden" name="ptr_phone" value="{{$info->phone}}">
                                                <input type="hidden" name="payment_type" value="0" class="payment_type">
                                                <span class="label-custom label-custom-danger cursor-pointer" onclick="cancelPaymentPage(true, {{count($val['list']) > 0 ? $val['cancelPrice'] : $val->amount}})">결제취소</span>
                                                {!! Form::close() !!}
                                            </td>
                                        @endif
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @if(count($val['list']) > 0)
                            <div class="col-xs-12 pg-history-list">
                                <div class="arr">
                                    <img src="../img/admin/re.svg" alt="">
                                </div>
                                <div class="box custom">
                                    <div class="box-header">
                                        <h3 class="box-title">결제내역</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="box-body chart-responsive" style="">
                                        <div class="pg-wrap">
                                            <div class="lists">
                                                <table>
                                                    <tr>
                                                        <th>결제번호</th>
                                                        <th>주문번호</th>
                                                        <th>승인정보1</th>
                                                        <th>승인정보2</th>
                                                        <th>승인날짜</th>
                                                        <th>결제금액</th>
                                                    </tr>
                                                    @foreach( $val['list'] as $pgList )
                                                        <tr>
                                                            <td>{{ $pgList->pg_transaction_no }}</td>
                                                            <td><a href="/payment/detail?search_payment_id={{$pgList->payment_id}}" target="_blank">{{ $pgList->payment_id}}</a></td>
                                                            <td>{{ $pgList->message1 }}</td>
                                                            <td>{{ $pgList->message2 }}</td>
                                                            <td>{{ $pgList->created_at }}</td>
                                                            <td>{{ number_format($pgList->amount) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div style='text-align: center; padding-top: 30px; padding-bottom: 30px; background: #fff'>조회된 데이터가 없습니다.</div>
                @endif
            </div>
            <div class="col-xs-12 tab-content" data-target="5">
                @if( $info->status == 3 || $info->status == 4 || $info->status == 5 )
                    <div class="row">
                        @if(isset($cgs_payment))
                            <div class="col-xs-12">
                                <div class="box box-primary">
                                    <div class="box-header">
                                        <h3 class="box-title">지급 상태</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="box-body chart-responsive">
                                        <div class="box-body chart-responsive" style="">
                                            <table class="custom-table">
                                                <tr>
                                                    <th>예정 입금일시</th>
                                                    <th>일 간병비</th>
                                                    <th>보험료</th>
                                                    <th>수수료</th>
                                                    <th>지급 유무</th>
                                                </tr>
                                                @forelse($cgs_payment as $row)
                                                    <tr @if($row->status == 'cancel') class="text-red" @elseif($row->status == 'payment') class="text-green" @endif>
                                                        <td>{{ $row->payment_at }}</td>
                                                        <td>{{ number_format($row->amount)  }}</td>
                                                        <td>{{ number_format($row->fee_insurance)  }}</td>
                                                        <td>{{ number_format($row->fee_company)  }}</td>
                                                        <td>{{ getPaymentStatus($row->status) }}</td>
                                                    </tr>
                                                @empty
                                                @endforelse

                                                @if(!empty($cgs_payment))
                                                    <tr>
                                                        <td colspan="5" class="text-right">
                                                            <B> 대기 : {{ number_format($cgs_payment->where('status', 'stay')->sum('amount')) }} </B> <br>
                                                            <B class="text-green"> 지급 : {{ number_format($cgs_payment->where('status', 'payment')->sum('amount')) }}</B> <br>
                                                            <B class="text-red">취소 : {{ number_format($cgs_payment->where('status', 'cancel')->sum('amount')) }}</B>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(isset($cgs_amount))
                            <div class="col-xs-12">
                                <div class="box box-primary">
                                    <div class="box-header">
                                        <h3 class="box-title">입금 상태</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="box-body chart-responsive">
                                        <div class="box-body chart-responsive" style="">
                                            <table class="custom-table">
                                                <tr>
                                                    <th>지급일시</th>
                                                    <th>지급액</th>
                                                    <th>보험료</th>
                                                    <th>수수료</th>
                                                    <th>정보</th>
                                                </tr>
                                                @forelse($cgs_amount as $row)
                                                    <tr>
                                                        <td>{{ $row->created_at }}</td>
                                                        <td>{{ number_format($row->amount)  }}</td>
                                                        <td>{{ number_format($row->fee_insurance)  }}</td>
                                                        <td>{{ number_format($row->fee_company)  }}</td>
                                                        <td>{{ $row->title }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">조회된 정보가 없습니다.</td>
                                                    </tr>
                                                @endforelse

                                                @if(!empty($cgs_payment))
                                                    <tr>
                                                        <td>합계</td>
                                                        <td >{{ number_format($cgs_amount->sum('amount'))}}</td>
                                                        <td class="text-red">{{ number_format($cgs_amount->sum('fee_insurance'))}}</td>
                                                        <td>{{ number_format($cgs_amount->sum('fee_company'))}}</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if(in_array(Auth::guard('admin')->user()->division_code, ['AD'])
                   || ( in_array(Auth::guard('admin')->user()->division_code, ['CS']) && Auth::guard('admin')->user()->email == "sombibi@carenation.kr" ))
                        <div class="col-xs-12">
                            <div class="text-right">
                                <button type="button" class="btn btn-danger" onclick="cancelCgsAmount(true, '{{ $info->info }}')" style="margin-bottom: 20px">간병비 지급</button>
                            </div>
                        </div>
                    @endif
                @else
                    <div style='text-align: center; padding-top: 30px; padding-bottom: 30px; background: #fff'>조회된 데이터가 없습니다.</div>
                @endif
            </div>
            <div class="col-xs-12 tab-content" data-target="6">
                @if(count($info['code']) > 0 )
                    <div class="box box-primary">
                        <div class="box-body pt0 custom-min-height">
                            <div class="basic-table-wrap">
                                <table id="cgsUsers" class="custom-table">
                                    <tr>
                                        <col width="5%">
                                        <col width="10%">
                                        <col width="20%">
                                        <col width="10%">
                                        <col width="10%">
                                    </tr>
                                    <tr>
                                        <th>No</th>
                                        <th>이름</th>
                                        <th>선호지역</th>
                                        <th>경력사항</th>
                                        <th>휴대전화</th>
                                    </tr>
                                    @foreach( $info['code'] as $item )
                                        <tr onclick=window.open("{{route('caregiver.detail',['id'=>$item['cgs_user']->id])}}") style="cursor:pointer;" >
                                            <td><span class="title">{{$item['cgs_user']->id}}</span></td>
                                            <td><span class="title">{{ $item['cgs_user']->name ?? '이름없음' }}</span></td>
                                            <td><span class="title">{{$gu_name['name']}}</span></td>
                                            <td><span class="title">{{$item['cgs_user']->experience == 0 || '' ? "없음": $item['cgs_user']->experience}}</span></td>
                                            <td><span class="sub-content desc">{{ $item['cgs_user']->phone }}</span></td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div style='text-align: center; padding-top: 30px; padding-bottom: 30px; background: #fff'>조회된 데이터가 없습니다.</div>
                @endif
            </div>
            <div class="col-xs-12 tab-content" data-target="7">
                @if(($info->status == 5) || ($info->status == 4 && $info->cancel_status == 'Y'))
                    <div class="box box-primary">
                        <div class="box-body custom-min-height pt0">
                            <form id="rating" action="{{ route('ptrjob.rating', ['id' => $id]) }}" method="post">
                                @csrf
                                <input type="hidden" name="rating_type" value="">
                                <div class="list-wrap">
                                    <ul class="rating">
                                        <li>
                                            <span class="title">NAME:</span>
                                            <span class="sub-content">
                                            {{ $info['cgs_name'] }}
                                        </span>
                                        </li>
                                        <li>
                                            <input type="hidden" name="rating" value="{{ $info['rating']->rating ?? '' }}">
                                            <span class="title">RATED:</span>
                                            <span class="sub-content rating-star">
                                            @for($i = 0; $i < 5; $i++)
                                                    <img src="{{ !empty($info['rating']) && $info['rating']->rating > $i ? '../img/admin/star_active.png' : '../img/admin/star.png' }}" data-value="{{ $i }}" alt="">
                                                @endfor
                                        </span>
                                        </li>
                                        <li>
                                            <span class="title">COMMENT:</span>
                                            <span class="sub-content">
                                            <textarea class="rating-comment" name="content">{{ !empty($info['rating']) ? $info['rating']->content : '' }}</textarea>
                                        </span>
                                        </li>
                                    </ul>
                                    <div class="rating-btn-wrap float-right">
                                        <button type="button" class="btn {{ !empty($info['rating']) ? 'btn-warning' : 'btn-primary' }} btn-rating" data-value="{{ !empty($info['rating']) ? 'update' : 'create' }}" onclick="ratingCreate();">{{ !empty($info['rating']) ? '수정' : '둥록' }}</button>
                                        <button type="button" class="btn btn-danger" onclick="removeData()">삭제</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div style='text-align: center; padding-top: 30px; padding-bottom: 30px; background: #fff'>조회된 데이터가 없습니다.</div>
                @endif
            </div>
            <div class="col-xs-12 tab-content" data-target="8">
                @if( ($info->status == 3||4||5) && $info->cancel_status == 'Y')
                    <div class="box box-primary">
                        <div class="box-body">
                            <h5>간병 시작일</h5>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                <input type="text" name="jobStartDate" value="{{ $info->job_start_date }}" class="form-control" readonly>
                            </div>
                            <h5>간병 종료일</h5>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                <input type="text" name="jobEndDate" value="{{ $info->job_end_date.' '.'('.getDiffDate($info->job_start_date, $info->job_end_date)['day'].'일'.' '.getDiffDate($info->job_start_date, $info->job_end_date)['hours'].'시간)' }}" class="form-control" readonly>
                            </div>
                            <h5>취소 시간</h5>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                <input type="text" name="created_at" value="{{ !empty($info->job_cancel_at) ? $info->job_cancel_at.' '.'(근무일수: '.getDiffDate($info->job_start_date, $info->job_cancel_at)['day'].'일'.' '.getDiffDate($info->job_start_date, $info->job_cancel_at)['hours'].'시간)': "-" }}" class="form-control" readonly>
                            </div>
                            <hr>
                            <h5>매칭 간병인 지원간병비 정보</h5>
                            <div class="box-body chart-responsive" style="">
                                <table id="cgsMatching" class="custom-table">
                                    <tr>
                                        <th>No</th>
                                        <th>아이디</th>
                                        <th>이름</th>
                                        <th>성별</th>
                                        <th>나이</th>
                                        <th>휴대전화</th>
                                        <th>{{ ($info->job_type == 'time') ? '시급' : '일급' }} 간병비</th>
                                        <th>총 간병비</th>
                                        <th>지원 상태</th>
                                        <th>회원 탈퇴 여부</th>
                                        <th>지원 시간</th>
                                    </tr>
                                    @foreach($info['applicant'] as $applicant)
                                        @if($applicant->status=='choice')
                                            <tr class=
                                                {{$applicant->status=='choice' ? "choice": ""}}
                                            >
                                                <td><a href="/caregiver/{{ $applicant['cgs_user']->id ?? '' }}" target="_blank">{{ $applicant['cgs_user']->id ?? '' }}</a></td>
                                                <td>{{ $applicant['cgs_user']->email ?? '' }}</td>
                                                <td><a href="{{route('caregiver.detail', ['id'=>$applicant['cgs_user']->id])}}" target="_blank">{{ $applicant['cgs_user']->name ?? '' }}</a></td>
                                                <td>{{ convert_gender($applicant['cgs_user']->gender) ?? '' }}</td>
                                                <td>{{ convert_age($applicant['cgs_user']->birthdate) ?? '' }}</td>
                                                <td>{{ $applicant['cgs_user']->phone ?? '' }}</td>
                                                <td>{{ number_format( ($info->job_type == 'time') ? $applicant->amount_time : $applicant->amount_day) }}</td>
                                                <td>{{ number_format($applicant->total) }}</td>
                                                <td>{{ $applicant->applicant_status ?? ''}}</td>
                                                <td>{{ $applicant->is_deleted ?? ''}}</td>
                                                <td>{{ $applicant->created_at}}</td>
                                            </tr>
                                            @endif
                                            @endforeach
                                            </tr>
                                </table>
                            </div>
                            <hr>
                            <h5>결제 내역</h5>
                            @if(count($paymentList) > 0)
                                @foreach($paymentList as $key=>$val)
                                    @if(count($val['list']) > 0)
                                        <div class="box-body chart-responsive" style="">
                                            <table class="custom-table" id="paymentDetail">
                                                <tr>
                                                    <th>결제번호</th>
                                                    <th>승인정보1</th>
                                                    <th>승인정보2</th>
                                                    <th>승인날짜</th>
                                                    <th>결제금액</th>
                                                </tr>
                                                @foreach( $val['list'] as $pgList )
                                                    <tr>
                                                        <td>{{ $pgList->pg_transaction_no }}</td>
                                                        <td>{{ $pgList->message1 }}</td>
                                                        <td class="approvalInfo">{{ $pgList->message2 }}</td>
                                                        <td>{{ $pgList->created_at }}</td>
                                                        <td class="approvalAmount">{{ number_format($pgList->amount) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div style='text-align: center; padding-top: 30px; padding-bottom: 30px; background: #fff'>조회된 데이터가 없습니다.</div>
                            @endif
                            <hr>
                            <h5>취소 내역</h5>
                            @if($info->cancel_status == 'Y')
                                <div class="box-body chart-responsive" style="">
                                    <table class="custom-table" id="ptrjobCancel">
                                        <tr>
                                            <th>취소 구분</th>
                                            <th>취소 사유</th>
                                            <th>취소 금액</th>
                                            <th>취소요청 사용자</th>
                                        </tr>
                                        <tr
                                            @if($approved_price == $cancel_price)
                                            class="totalCancel"
                                            @else
                                            class="partCancel"
                                            @endif
                                        >
                                            @if($approved_price == $cancel_price)
                                                <td>전액 취소</td>
                                            @else
                                                <td>부분 취소</td>
                                            @endif
                                            <td>{{ isset($info->deleted_type) ? getJobDeletedType($info->deleted_type) : '-'}}</td>
                                            <td id="cancelAmount">{{ isset($cancel_price) ? number_format($cancel_price) : '-'}}</td>
                                            <td>{{ isset($info->cancel_user) ? getVersionType($info->cancel_user) : '-'}}</td>
                                        </tr>
                                    </table>
                                </div>
                            @else
                                <div style='text-align: center; padding-top: 30px; padding-bottom: 30px; background: #fff'>조회된 데이터가 없습니다.</div>
                            @endif
                        </div>
                    </div>
                    @if(isset($calInfo))
                        <div class="row">
                            @php($calInfoTypeArr = ['payment','cost','cancel'])
                            @foreach($calInfoTypeArr as $infoType)
                                <div class="col-xs-4">
                                    <div class="box box-primary">
                                        <div class="box-header">
                                            <h3 class="box-title">
                                                @switch($infoType)
                                                    @case("payment") 공고 정보 @break
                                                    @case("cost") 간병 정보 @break
                                                    @case("cancel") 환불 정보 @break
                                                @endswitch
                                            </h3>
                                        </div>
                                        <div class="box-body">
                                            <table class="custom-table">
                                                <tr>
                                                    <th>일수 : </th>
                                                    @if($infoType =='payment')
                                                        <td>
                                                            {{ getDiffDate($info->job_start_date, $info->job_end_date)['day'].'일'.' '.getDiffDate($info->job_start_date, $info->job_end_date)['hours'].'시간' }}
                                                        </td>
                                                    @elseif($infoType == 'cancel')
                                                        <td>
                                                            {{ getDiffDate($info->job_cancel_at, $calInfo[$infoType]['date_info']['stop'])['day'].'일'.' '.getDiffDate($info->job_cancel_at, $calInfo[$infoType]['date_info']['stop'])['hours'].'시간' }}
                                                        </td>
                                                    @else
                                                        <td>{{ $calInfo[$infoType]['date_info']['diff']['day'] }} 일 {{ $calInfo[$infoType]['date_info']['diff']['hours'] }}시간</td>
                                                    @endif
                                                </tr>
                                                <tr>
                                                    <th>시작 일시 : <br>종료 일시 :</th>
                                                    <td>
                                                        @if($infoType == 'cancel')
                                                            {{ $info->job_cancel_at }}
                                                        @else
                                                            {{ $calInfo[$infoType]['date_info']['start'] }}
                                                        @endif
                                                        <br>
                                                        @if( $infoType == 'cost' )
                                                            {{ $info->job_cancel_at }}
                                                        @elseif($infoType == 'cancel')
                                                            {{ $calInfo[$infoType]['date_info']['stop'] }}
                                                        @else
                                                            {{ $calInfo[$infoType]['date_info']['stop'] }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>결제 금액 : </th>
                                                    @switch($infoType)
                                                        @case("payment") <td>{{ number_format($calInfo[$infoType]['payment'] )}}원</td> @break
                                                        @case("cost") <td>{{ number_format($calInfo[$infoType]['payment'] )}}원</td> @break
                                                        @case("cancel") <td class="payment_point">{{ number_format($calInfo[$infoType]['payment'] )}}원</td> @break
                                                    @endswitch
                                                </tr>
                                                <tr>
                                                    <th> ↳ PG 수수료 ① : </th>
                                                    <td>{{ number_format($calInfo[$infoType]['pg_fee']['sum'])}}원</td>
                                                </tr>
                                                <tr>
                                                    <th style="border-top: 3px double; border-bottom: 1px solid">PG사 입금 금액 : </th>
                                                    <td style="border-top: 3px double; border-bottom: 1px solid">{{ number_format($calInfo[$infoType]['payment']-$calInfo[$infoType]['pg_fee']['sum'] )}}원</td>
                                                </tr>

                                                <tr>
                                                    <th> 총 간병인 비용 :</th>
                                                    <td>{{ number_format($calInfo[$infoType]['cgs_payment'])}}원</td>
                                                </tr>
                                                <tr>
                                                    <th> ↳ 간병인 수수료 ② : </th>
                                                    <td>{{ number_format($calInfo[$infoType]['cgs_fee'] )}}원</td>
                                                </tr>
                                                <tr>
                                                    <th> ↳ 보험료 : </th>
                                                    <td>{{ number_format($calInfo[$infoType]['insurance_amount'])}}원</td>
                                                </tr>
                                                <tr>
                                                    <th style="border-top: 3px double;  border-bottom: 1px solid">간병인 총 급여 : </th>
                                                    <td style="border-top: 3px double;  border-bottom: 1px solid">{{ number_format($calInfo[$infoType]['cgs_payment']-$calInfo[$infoType]['cgs_fee']-$calInfo[$infoType]['insurance_amount'] )}}원</td>
                                                </tr>
                                                <tr>
                                                    <th> 보호자 수수료③ : </th>
                                                    <td>{{ number_format($calInfo[$infoType]['ptr_fee'])}}원</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        수수료 합계 (②+③)<br>
                                                        매출액 (①+②+③)
                                                    </td>
                                                    <td>{{ number_format($calInfo[$infoType]['ptr_fee'] + $calInfo[$infoType]['cgs_fee'])}}원<br>
                                                        {{ number_format($calInfo[$infoType]['pg_fee']['sum'] + $calInfo[$infoType]['ptr_fee'] + $calInfo[$infoType]['cgs_fee'])}}원
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div style='text-align: center; padding-top: 30px; padding-bottom: 30px; background: #fff'>조회된 데이터가 없습니다.</div>
                @endif
            </div>
        </div>

        {{-- popup --}}
        <div class="pop-wrap ptr_job_bank">
            <div class="pop-body">
                <div class="pop-header">
                    <h2>금액 입력하기</h2>
                    <img src="../img/admin/close.png" class="pop-close" alt="">
                </div>
                <div class="pop-content">
                    <h5>받은 금액입력</h5>
                    <div class="input-group">
                        <input type="text" name="amount" class="form-control" placeholder="받은 금액을 입력해주세요">
                    </div>
                    <h5>기타</h5>
                    <div class="input-group">
                        <textarea class="form-control" name="etc" rows="3"></textarea>
                    </div>
                </div>
                <div class="pop-footer">
                    <div class="btn-wrap">
                        <button type="button" class="btn btn-danger pop-close">닫기</button>
                        <button type="button" class="btn btn-primary" onclick="ptrPayment()">확인</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('script')
    <script>
        var routes = {
            detail: "{!! route('ptrjob.detail', ['id' => $id]) !!}",
            delete: "{!! route('ptrjob.rating_delete', ['id' => $id]) !!}",
            bankCreate: "{!! route('ptrjob.payment.create', ['id' => $id]) !!}",
            bankDelete: "{!! route('ptrjob.payment.delete', ['id' => $id]) !!}",
            jobAmount: "{!! route('cgsamount.job.amount') !!}",
            oneClickCancel: "{!! route('ptrjob.onClickCancel') !!}",
            cashPayment: "{!! route('ptrjob.cash', ['id' => $id]) !!}",
            cashPaymentCancel: "{!! route('ptrjob.cash.cancel', ['id' => $id]) !!}"
        };

        const amount_data = {
            cgs_users_id: "{!! $info->cgs_users_id !!}",
            ptr_job_id: "{!! $info->id !!}",
        };

        $('.btn-rating').click(function(){
            var status = $(this).attr('data-value');
            $('input[name=rating_type]').val(status);
        });

        $('.btn-job').click(function(){
            var status = $(this).attr('data-value');
            $('input[name=cancel_type]').val(status);
        });

        var list = [];
        $('.rating-star img').click(function(){
            list = [];
            var obj = $(this);
            var num = $(this).index();
            var rating = $('input[name=rating]').val();
            if(rating == num+1){
                $('.rating-star img').attr('src', '../img/admin/star.png');
                $('input[name=rating]').val('');
            } else {
                $('input[name=rating]').val(num+1);
                $('.rating-star img').each(function(idx){
                    $('.rating-star img').eq(idx).attr('src', '../img/admin/star.png');
                    var data = $(this).attr('data-value');
                    if( data <= num ){
                        list.push(data);
                        $('.rating-star img').eq(list[idx]).attr('src', '../img/admin/star_active.png');
                    }
                });
            }
        });

        $('.btn_bank').click(function(){
            $('.ptr_job_bank').addClass('active');
        });

        $('.tab-item').click(function(){
            let _this = $(this);
            let val = $(this).index();
            _this.addClass('active').siblings('.tab-item').removeClass('active');
            $('.tab-content').each(function(){
                let _this = $(this);
                let data = $(this).attr('data-target');
                if(val == data){
                    _this.addClass('active').siblings('.tab-content').removeClass('active');
                }
            });
        });

        function paymentCashAjax(jobId, amount){
            $.ajax({
                url: routes.cashPayment,
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'jobId' : jobId,
                    'amount' : amount
                },
                success: function (res) {
                    if(res.code == 200){
                        paymentCashInfo(res.code, res.message);
                    } else {
                        paymentCashInfo(res.code, res.message);
                    }
                }
            });
        }

        function paymentCashCancelAjax(jobId, amount, searchDate, cancelUsr){
            $.ajax({
                url: routes.cashPaymentCancel,
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'jobId' : jobId,
                    'amount' : amount,
                    'searchDate' : searchDate,
                    'cancelUsr' : cancelUsr,
                },
                success: function (res) {
                    if(res.code == 200){
                        paymentCashInfo(res.code, res.message);
                    } else {
                        paymentCashInfo(res.code, res.message);
                    }
                }
            });
        }

        function paymentCash(bool = true) {
            if(bool) {
                let amount = "{!! count($paymentList) > 0 ? $paymentList[0]->amount : 0 !!}";
                let jobDate = "{!! $info->job_start_date.' ~ '.$info->job_end_date !!}";
                swal({
                    title: '현금입금',
                    html:
                        '<div class="job_info_cash">' +
                        '    <p>공고 시간: <span>'+jobDate+'</span></p>' +
                        '    <p>총 가격: <span>'+number_format(amount)+'</span> 원</p>' +
                        '</div>'+
                        '<div class="form-group">' +
                        '    <h5>금액</h5>' +
                        '    <div class="input-group">' +
                        '        <span class="input-group-addon"><i class="fa fa-building"></i></span>' +
                        '        <input type="text" name="cash_amount" value="'+amount+'" class="form-control">' +
                        '    </div>' +
                        '</div>',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '확인',
                    cancelButtonText: '취소',
                }).then(function(res) {
                    if(res.value){
                        let jobId = "{!! $id !!}";
                        let cash_amount = $('input[name=cash_amount]').val();
                        if(Number(cash_amount) != Number(amount)){
                            CashCheckFail(amount);
                        } else {
                            CashCheck(jobId, amount);
                        }
                    }
                });
            }
        }

        function paymentCashInfo(code, message) {
            swal({
                title: '알림',
                html: message,
                type: code == 200 ? 'success' : 'warning',
                confirmButtonText: '확인',
            });
        }

        function paymentCashCancel(bool = true) {
            if(bool) {
                let amount = "{!! count($paymentList) > 0 ? $paymentList[0]->cancelPrice : 0 !!}";
                swal({
                    title: '현금결제 취소',
                    html:
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
                        '</div>'     +
                        '<hr>' +
                        '<div class="form-group">' +
                        '    <div class="radio">' +
                        '        <label>' +
                        '        <input type="radio" name="payment" id="batch" class="payment-radio" value="0" checked>' +
                        '        전액취소' +
                        '        </label>' +
                        '    </div>' +
                        '    <div class="radio">' +
                        '        <label>' +
                        '        <input type="radio" name="payment" id="division" class="payment-radio" value="3">' +
                        '        분할취소' +
                        '        </label>' +
                        '    </div>' +
                        '</div>' +
                        '<hr>' +
                        '<div class="form-group">' +
                        '    <label for="exampleInputEmail1" class="float-left">취소 금액 (취소 가능금액: '+number_format(amount)+')</label>' +
                        '    <input type="number" placeholder="부분 취소 금액을 입력해주세요." id="cancel" class="form-control" value="'+amount+'">' +
                        '</div>' +
                        '<span class="payment-error">금액을 초과하였습니다. 최대 '+number_format(amount)+'원 까지만 환불 가능합니다.</span>'+
                        '<div class="form-group" >' +
                        '    <label for="searchDate" class="float-left">날짜 선택</label>' +
                        '    <input type="text" id="searchDate" name="searchDate" class="form-control" autocomplete="off" placeholder="날짜 선택">' +
                        '</div>' ,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '확인',
                    cancelButtonText: '취소',
                    onOpen: function(){
                        $('#searchDate').parent().hide();

                        $('input[name=payment]').change(function(){
                            $('input[name=payment]').each(function(){
                                let paymentType = $(this).val();
                                let checked = $('#batch').prop('checked');
                                let checkedDivision = $('#division').prop('checked');

                                if( checked ){
                                    $('#cancel').val(amount);
                                } else {
                                    $('#cancel').val("");
                                }

                                //분할취소 클릭시
                                if( checkedDivision ){
                                    getCashDiv();
                                }else{
                                    $('#searchDate').parent().hide();
                                }

                                $('.payment_type').val(paymentType);
                            });
                        });
                    },
                }).then(function(res) {
                    if(res.value){
                        let jobId = "{!! $id !!}";
                        let cancel = $('#cancel').val();
                        let searchDate = $('input[name=searchDate]').val();
                        let cancelUsr = $('input[name=cancel]').val();

                        if( Number(cancel) > Number(amount) ){
                            cancelCashCheckFail(amount);
                        } else {
                            cancelCashCheck(jobId, cancel, searchDate, cancelUsr);
                        }
                    }
                });
            }
        }

        // 분할 취소 일시 선택
        function getCashDiv() {
            $('#searchDate').parent().show();
            $('#searchDate').datetimepicker({
                format: 'YYYY-MM-DD HH:00:00',
                sideBySide: true,
                showClose: true,
                toolbarPlacement: "bottom",
                icons: {
                    close: 'btn_apply'
                },
                minDate: '{!! isset($info) ? $info->job_start_date : '' !!}',
                maxDate: '{!! isset($info) ? $info->job_end_date : '' !!}'
            }).on('dp.hide', function () {
                let value = $(this).val();
                $('input[name=searchDate]').val(value);
                $('#searchForm').submit();
            });
        }

        // 결제 확인
        function CashCheck(jobId, amount) {
            swal({
                title: '알림',
                html: '<div><p>승인하실 금액: <strong>'+number_format(amount)+'원</strong></p></div>',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '확인',
                cancelButtonText: '취소',
            }).then(function(res) {
                if(res.value){
                    paymentCashAjax(jobId, amount);
                }
            });
        }

        // 결제 취소 확인
        function cancelCashCheck(jobId, amount, searchDate, cancelUsr) {
            swal({
                title: '알림',
                html: '<div><p>취소하실 금액: <strong>'+number_format(amount)+'원</strong></p></div>',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '확인',
                cancelButtonText: '취소',
            }).then(function(res) {
                if(res.value){
                    paymentCashCancelAjax(jobId, amount, searchDate, cancelUsr);
                }
            });
        }

        // 결제 취소 확인
        function cancelCashCheckFail(amount) {
            swal({
                title: '알림',
                html: '<div><p>금액이 초과되었습니다.<br> 금액은 <strong>'+number_format(amount)+'원</strong> 아래로만 가능합니다.</p></div>',
                type: 'warning',
                confirmButtonText: '확인',
            })
        }

        // 결제 취소 확인
        function CashCheckFail(amount) {
            swal({
                title: '알림',
                html: '<div><p>입금액이 다릅니다.<br> 금액은 <strong>'+number_format(amount)+'원</strong>만 가능합니다.</p></div>',
                type: 'warning',
                confirmButtonText: '확인',
            })
        }

        {{--$('#searchDate').datetimepicker({--}}
        {{--    format: 'YYYY-MM-DD HH:00:00',--}}
        {{--    sideBySide: true,--}}
        {{--    showClose: true,--}}
        {{--    toolbarPlacement: "bottom",--}}
        {{--    icons: {--}}
        {{--        close: 'btn_apply'--}}
        {{--    },--}}
        {{--    minDate: '{!! isset($calInfo) ? $calInfo['payment']['date_info']['start'] : '' !!}',--}}
        {{--    maxDate: '{!! isset($calInfo) ? $calInfo['payment']['date_info']['stop'] : '' !!}'--}}
        {{--}).on('dp.hide', function () {--}}
        {{--    let value = $(this).val();--}}
        {{--    $('input[name=searchDate]').val(value);--}}
        {{--    $('#searchForm').submit();--}}
        {{--});--}}

        {{--let searchDate = '{!! !empty($_GET['searchDate']) ? $_GET['searchDate'] : '' !!}';--}}
        {{--$('input[name=searchDate]').val(searchDate);--}}

    </script>
    <script src="/pages/admin/ptrjob/page.ptrjob.func.js"></script>
@endsection

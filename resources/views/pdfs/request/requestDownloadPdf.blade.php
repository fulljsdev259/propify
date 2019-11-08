@extends('pdfs.request.layout')
@section('title')
@lang("models.request.download_pdf.service_request")
@endsection

@section('body')
<table class="data_table" cellpadding="0" cellspacing="0">

    <tbody>
        <tr>
            <td colspan="2" width="100%" class="no_border">
                        <table width="100%" style="vertical-align:middle;margin-bottom: 15px;">
                            <tbody>
                                <tr>
                                    <td class="table_header" valign="middle">
                                        <span style="display: inline-block;vertical-align:top;">@lang('models.request.download_pdf.captured_by') {{$request->creator->email}} {{ now()->format('d.m.Y, H:i') }}</span>
                                        <span style="display: inline-block;height:25px;width:100%;"></span>
        
                                        <span>{{ $request->service_request_format }}</span> <br />
        
                                        <p style="margin:7px 0 0;">
                                                <b>@lang('models.request.download_pdf.request_number'):</b>
                                                {{ $request->request_format }}
        
                                            </p>
                                        <p style="margin:7px 0 0;">
                                            <b>@lang('models.request.category'):</b>
                                            @if (! empty($subCategory))
                                            {{ !empty($category['name']) ? __('models.request.category_list.' . $category['name']) . ' > ' : ''  }}
                                            {{ __('models.request.sub_category.' . $subCategory['name']) }}
                                            @elseif (! empty($category['name']))
                                            {{ __('models.request.category_list.' . $category['name']) }}
                                            @endif
        
                                        </p>
                                        <p style="margin:7px 0 0;">
                                            <b>@lang('models.request.status.label'):</b>
                                            @lang('models.request.status.'.\App\Models\Request::Status[$request->status])
                                            ({{ now()->format('d.m.Y, H:i') }})
                                        </p>
                                        <p style="margin:7px 0 0;">
                                            <b>@lang('general.address'):</b>
                                            {{ @$contract->building->address->street }}
                                            {{ @$contract->building->address->house_num }},
                                            {{ @$contract->building->address->zip }}
                                            {{ @$contract->building->address->city }}
        
                                        </p>
                                    </td>
                                    
                                    @if(!$blank_pdf)
                                    <td style="text-align:right;vertical-align:top;" class="table_header">
                                        <img class="logo" src="{{public_path($logo)}}" />
                                    </td>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
            </td>
        </tr>
        <tr>
            <td class="no_border" width="100%">
                <h2 style="margin-bottom:10px;margin-top:0;font-size: 25px;">@lang('models.request.download_pdf.details')</h2>
            </td>
        </tr>
        <tr>
                <td colspan="2" width="100%" class="no_border">
                    <table width="100%">
                        <tbody>
                            <tr>
                                <td width="100%" class="no_border">
                                    <table class="inner_table" width="100%">
                                        <tbody>
   
                                            <tr>
                                                @if( (!empty($category['capture_phase']) && $category['capture_phase'] == 1)
                                                || (!empty($subCategory['capture_phase']) && $subCategory['capture_phase']
                                                == 1) )
   
                                                <td class="no_border">
                                                    <strong>
                                                        @lang('models.request.category_options.capture_phase'): 
                                                    </strong>
                                                    @if(key_exists($request->capture_phase,
                                                    \App\Models\Request::CapturePhase))
                                                    @lang('models.request.capture_phase.' .
                                                    \App\Models\Request::CapturePhase[$request->capture_phase])
                                                    @endif
                                                </td>
   
                                                @endif
   
                                                
                                                @if( (!empty($category['qualification']) && $category['qualification'] == 1)
                                                || (!empty($subCategory['qualification']) && $subCategory['qualification']
                                                == 1) )
                                                <td class="no_border">
                                                   <strong>
                                                       @lang('models.request.qualification.label'): 
                                                   </strong>
                                                   @lang('models.request.qualification.'.\App\Models\Request::Qualification[$request->qualification])
                                               </td>
   
                                                @endif
                                            </tr>
   
   
                                            <tr>
                                                <td class="border_btm" @if (
                                                    !((((!empty($category['location']) && $category['location'] == 1) ||
                                                    (!empty($subCategory['location']) && $subCategory['location'] == 1))) ||
                                                    ((!empty($category['room']) && $category['room'] == 1) ||
                                                    (!empty($subCategory['room']) && $subCategory['room'] == 1)))
                                                )
                                                    colspan="2"
                                                @endif>
                                                    <strong>@lang('models.request.category_options.component'): 
                                                    </strong>
                                                    {{ $request->component }}
                                                </td>
   
   
   
                                                @if( (!empty($category['location']) && $category['location'] == 1) ||
                                                (!empty($subCategory['location']) && $subCategory['location'] == 1) )
                                                <td class="border_btm">
                                                    <strong> @lang('models.request.category_options.range'): 
                                                    </strong>
                                                    @if(key_exists($request->location, \App\Models\Request::Location))
                                                    @lang('models.request.location.' .
                                                    \App\Models\Request::Location[$request->location]);
                                                    @endif
                                                </td>
                                                
   
                                                @elseif( (!empty($category['room']) && $category['room'] == 1) ||
                                                (!empty($subCategory['room']) && $subCategory['room'] == 1) )
                                                <td class="border_btm">
                                                   <strong>@lang('models.request.category_options.room'): 
                                                   </strong>
                                                   @if(key_exists($request->room, \App\Models\Request::Room))
                                                   @lang('models.request.room.' .
                                                   \App\Models\Request::Room[$request->room])
                                                   @endif
                                               </td>
                                               
                                                @endif
   
                                            </tr>
   
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
   
           </tr>
           <tr>
               <td class="no_border" width="100%">
                   <h4 style="margin-bottom:0;font-size: 14px;">@lang('general.title'):</h4>
                   <p style="display:block;width:100%;margin-top:5px;">{{ $request->title }}</p>
               </td>
           </tr>
           <tr>
               <td colspan="2" class="no_border" width="100%">
                   <h4 style="margin-bottom:0;font-size: 14px;">@lang('general.description'):</h4>
                   <p style="display:block;width:100%;margin-top:5px;">{!! $request->description !!} </p>
               </td>
           </tr>
           <tr>
               <td class="no_border">
                   <h4 style="margin-bottom:0;font-size: 20px;">@lang('models.request.download_pdf.contact_details'):</h4>
               </td>
           </tr>
           <tr>
               <td colspan="2" class="no_border" width="100%">
                   <p style="display:block;width:100%;margin-top:5px;">@lang('models.request.download_pdf.contact_text')
                   </p>
               </td>
           </tr>
           <tr>
               <td colspan="2" width="100%" class="no_border">
                   <table width="100%">
                       <tbody>
                           <tr>
                               <td class="no_border" width="100%">
                                   <table class="info_table" width="100%">
                                       <tbody>
                                           <tr>
                                               <td class="no_border" style="width:55%;"><strong>@lang('general.name'): </strong>{{ $resident->user->name }}</td>
                                               <td class="no_border"><strong>@lang('general.email'): </strong>{{ $resident->user->email }}</td>
                                           </tr>
   
                                           <tr>
                                               @if($resident->mobile != null)
                                               <td class="border_btm" colspan="2">
                                                   <strong>@lang('models.resident.mobile_phone'): </strong>{{ $resident->mobile }}</td>
                                               @else
                                               <td class="border_btm" colspan="2">
                                                   <strong>@lang('models.resident.private_phone'): </strong>{{ $resident->private_phone }}</td>
                                               @endif
                                           </tr>
                                       </tbody>
                                   </table>
                               </td>
                           </tr>
                       </tbody>
                   </table>
               </td>
           </tr>

        <tr>
                <td colspan="6" width="100%" class="no_border">
                    <table width="100%">
                        <tbody>
                            <tr>
                                <td class="no_border" width="100%">
                                    <table class="info_table" width="100%">
                                        <tbody>
                                            <tr>
                                                <td class="no_border" width="115%" style="margin:50px 0;">
                                                    <span
                                                        style="margin-top:20px;border-bottom:2px dotted #888;padding-bottom:30px;display:inline-block;width:85%;float:left;">
                                                        @lang('models.request.download_pdf.customer_signature')
                                                    </span>
                                                </td>
                                                <td class="no_border" width="100%" style="margin:50px 0;">
                                                    <span
                                                        style="margin-top:20px;border-bottom:2px dotted #888;padding-bottom:30px;display:inline-block;width:95%;float:left;">
                                                        @lang('models.request.download_pdf.entrepreneur_signature')
                                                    </span>
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <td style="width:100%;height:50px;border:none;">
                                                </td>
                                            </tr>
                                            @if($category['name'])
                                            <tr>
                                                <td colspan="2" class="no_border" width="100%" style="margin-top:50px;">
                                                    <span
                                                        style="margin-top:20px;padding-bottom:30px;display:inline-block;width:100%;">
                                                        @lang('models.request.download_pdf.blank_pdf')
                                                    </span>
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        @foreach($media as $i => $m)
        @if($i%2==0)
        <tr>
            <td colspan="2" width="100%" class="no_border">
                <table width="100%">
                    <tbody>
                        @if($i==0)
                        <tr>
                                <td class="no_border" width="100%">
                                    <h4 style="font-size: 14px;margin-buttom:45px;">@lang('models.request.pictures_of') 
                                        @if (! empty($category['name']))
                                            {{$category['name']}}
                                        @endif
                                    </h4>
                                            
                                </td>
                                <td></td>
                            </tr>
                        @endif
                        <tr>
                            <td class="no_border" width="100%">
                                    <span style="display: inline-block;height:25px;width:100%;"></span>
                                <table class="info_table" width="100%">
                                    <tbody>
                                        <tr style="vertical-align: top;">

                                            @endif  
                                            <td class="no_border" style="top:0;width:295px;vertical-align: top;">
                                                
                                                    <img class="pdf_attached" src="{{$m['file_path']}}"  />
                                            </td>
                                            @if($i%2==0)
                                            <td class="no_border">
                                                    <span class="sepearation"></span>
                                                </td>
                                            @endif
                                                    
                                            @if($i%2==1)
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        @endif
        @endforeach
    </tbody>
    
</table>
@endsection
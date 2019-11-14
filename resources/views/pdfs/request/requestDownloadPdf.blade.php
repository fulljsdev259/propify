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
                                        <span class="text_font" style="display: inline-block;vertical-align:top;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.download_pdf.captured_by') {{$request->creator->email}} {{ now()->format('d.m.Y H:i') }}</span>
                                        <span style="display: inline-block;height:25px;width:100%;"></span>
        
                                        <span style="font-family: {{$pdf_font_family ?? 'Arial'}};">{{ $request->service_request_format }}</span> <br />
        
                                        <p style="margin:7px 0 0;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                <b style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.download_pdf.request_number'):</b>
                                                {{ $request->request_format }}
        
                                            </p>
                                        <p style="margin:7px 0 0;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                            <b style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.category'):</b>
                                            @if (! empty($subCategory))
                                            {{ !empty($category['name']) ? __('models.request.category_list.' . $category['name']) . ' > ' : ''  }}
                                            {{ __('models.request.sub_category.' . $subCategory['name']) }}
                                            @elseif (! empty($category['name']))
                                            {{ __('models.request.category_list.' . $category['name']) }}
                                            @endif
        
                                        </p>
                                        <p style="margin:7px 0 0;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                            <b style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.status.label') ({{ now()->format('d.m.Y, H:i') }}):</b>
                                            @lang('models.request.status.'.\App\Models\Request::Status[$request->status])
                                            
                                        </p>
                                    </td>
                                    
                                    @if(!$blank_pdf)
                                    <td style="text-align:right;vertical-align:top;font-family: {{$pdf_font_family ?? 'Arial'}};" class="table_header">
                                        <img class="logo" src="{{public_path($logo)}}" />
                                    </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="no_border" colspan="2">

                                        <p style="margin:0;margin-top:-30px;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                            <b>@lang('models.request.download_pdf.address'):</b>
                                            {{ @$contract->building->address->street }}
                                            {{ @$contract->building->address->house_num }},
                                            {{ @$contract->building->address->zip }}
                                            {{ @$contract->building->address->city }}
        
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
            </td>
        </tr>
        <tr>
            <td class="no_border" width="100%">
                <h2 style="margin-bottom:10px;margin-top:15px;font-size: 25px;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.download_pdf.details')</h2>
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

                                            @if( (!empty($category['capture_phase']) && $category['capture_phase'] == 1)
                                            || (!empty($subCategory['capture_phase']) && $subCategory['capture_phase']
                                            == 1) || (!empty($category['qualification']) && $category['qualification'] == 1)
                                            || (!empty($subCategory['qualification']) && $subCategory['qualification']
                                            == 1) )
                                            <tr>
                                                @if( (!empty($category['capture_phase']) && $category['capture_phase'] == 1)
                                                || (!empty($subCategory['capture_phase']) && $subCategory['capture_phase']
                                                == 1) )
   
                                                <td class="no_border" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                    <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">
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
                                                <td class="no_border" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                   <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                       @lang('models.request.qualification.label'): 
                                                   </strong>
                                                   @lang('models.request.qualification.'.\App\Models\Request::Qualification[$request->qualification])
                                               </td>
   
                                                @endif
                                            </tr>
                                            @endif
   
                                            @if((isset($category['component']) && $category['component']==1) || 
+                                            (isset($subCategory['component']) && $subCategory['component']==1) || 
+                                            (!empty($category['location']) && $category['location'] == 1) ||
+                                            (!empty($subCategory['location']) && $subCategory['location'] == 1) ||
+                                            (!empty($category['room']) && $category['room'] == 1) ||
+                                            (!empty($subCategory['room']) && $subCategory['room'] == 1))
                                            <tr>
                                                @if((isset($category['component']) && $category['component']==1) || 
+                                                (isset($subCategory['component']) && $subCategory['component']==1))
                                                <td class="border_btm" style="font-family: {{$pdf_font_family ?? 'Arial'}};" @if (
                                                    !((((!empty($category['location']) && $category['location'] == 1) ||
                                                    (!empty($subCategory['location']) && $subCategory['location'] == 1))) ||
                                                    ((!empty($category['room']) && $category['room'] == 1) ||
                                                    (!empty($subCategory['room']) && $subCategory['room'] == 1)))
                                                )
                                                    colspan="2"
                                                @endif>
                                                    <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.category_options.component'): 
                                                    </strong>
                                                    {{ $request->component }}
                                                    
                                                </td>
                                                @endif
   
   
   
                                                @if( (!empty($category['location']) && $category['location'] == 1) ||
                                                (!empty($subCategory['location']) && $subCategory['location'] == 1) )
                                                <td class="border_btm" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                    <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};"> @lang('models.request.category_options.range'): 
                                                    </strong>
                                                    @if(key_exists($request->location, \App\Models\Request::Location))
                                                    @lang('models.request.location.' .
                                                    \App\Models\Request::Location[$request->location]);
                                                    @endif
                                                </td>
                                                
   
                                                @elseif( (!empty($category['room']) && $category['room'] == 1) ||
                                                (!empty($subCategory['room']) && $subCategory['room'] == 1) )
                                                <td class="border_btm" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                   <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.category_options.room'): 
                                                   </strong>
                                                   @if(key_exists($request->room, \App\Models\Request::Room))
                                                   @lang('models.request.room.' .
                                                   \App\Models\Request::Room[$request->room])
                                                   @endif
                                               </td>
                                               
                                                @endif
   
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
           <tr>
               <td class="no_border" width="100%" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                   <h4 style="margin-bottom:0;font-size: 14px;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('general.title'):</h4>
                   <p style="display:block;width:100%;margin-top:5px;font-family: {{$pdf_font_family ?? 'Arial'}};">{{ $request->title }}</p>
               </td>
           </tr>
           <tr>
               <td colspan="2" class="no_border" width="100%" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                   <h4 style="margin-bottom:0;font-size: 14px;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('general.description'):</h4>
                   <p style="display:block;width:100%;margin-top:5px;font-family: {{$pdf_font_family ?? 'Arial'}};">{!! $request->description !!} </p>
               </td>
           </tr>
           <tr>
               <td class="no_border" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                   <h4 style="margin-bottom:0;margin-top:-10px;font-size: 20px;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.download_pdf.contact_details')</h4>
               </td>
           </tr>
           <tr>
               <td colspan="2" class="no_border" width="100%" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                   <p style="display:block;width:100%;margin-top:5px;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.download_pdf.contact_text')
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
                                               <td class="no_border" style="width:55%;font-family: {{$pdf_font_family ?? 'Arial'}};"><strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('general.name'): </strong>{{ $resident->user->name }}</td>
                                               <td class="no_border" style="font-family: {{$pdf_font_family ?? 'Arial'}};"><strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('general.email'): </strong>{{ $resident->user->email }}</td>
                                           </tr>
   
                                           <tr>
                                               @if($resident->mobile != null)
                                               <td class="border_btm" colspan="2" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                   <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.resident.mobile_phone'): </strong>{{ $resident->mobile }}</td>
                                               @else
                                               <td class="border_btm" colspan="2" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                   <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.resident.private_phone'): </strong>{{ $resident->private_phone }}</td>
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
               <td colspan="2" width="100%" height="20px" style="border:none;font-family: {{$pdf_font_family ?? 'Arial'}};"></td>
           </tr>

        <tr>
                <td @if(count($media)==0) colspan='2' @else colspan='6'@endif width="100%" class="no_border" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                    <table width="100%">
                        <tbody>
                            <tr>
                                <td class="no_border" width="100%">
                                    <table class="info_table" width="100%">
                                        <tbody>
                                            <tr>
                                                <td class="no_border" width="115%" style="margin:50px 0;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                    <span
                                                        style="font-family: {{$pdf_font_family ?? 'Arial'}};margin-top:20px;border-bottom:2px dotted #888;padding-bottom:30px;display:inline-block;width:85%;float:left;height:35px;">
                                                        @lang('models.request.download_pdf.customer_signature')
                                                    </span>
                                                </td>
                                                <td class="no_border" width="100%" style="margin:50px 0;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                    <span
                                                        style="font-family: {{$pdf_font_family ?? 'Arial'}};margin-top:20px;border-bottom:2px dotted #888;padding-bottom:30px;display:inline-block;width:95%;float:left;height:35px;">
                                                        @lang('models.request.download_pdf.entrepreneur_signature')
                                                    </span>
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <td style="width:100%;height:75px;border:none;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="no_border" width="100%" style="float:;margin-top:50px;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                    <span
                                                        style="font-family: {{$pdf_font_family ?? 'Arial'}};margin-top:20px;padding-bottom:30px;display:inline-block;width:100%;">
                                                        @lang('models.request.download_pdf.blank_pdf')
                                                    </span>
                                                </td>
                                            </tr>
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
                                    <h4 style="font-size: 20px;margin-buttom:45px;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.pictures_of')
                                    </h4>
                                            
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="no_border" width="100%">
                                @if($i==0)
                                    <span style="display: inline-block;height:25px;width:100%; font-family: {{$pdf_font_family ?? 'Arial'}};"></span>
                                    @endif
                                <table class="info_table" width="100%">
                                    <tbody>
                                        <tr style="vertical-align: top;">

                                            @endif  
                                            <td class="no_border" style="top:0;width:292px;vertical-align: top;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                
                                                    <img class="pdf_attached" src="{{$m['file_path']}}"/>
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
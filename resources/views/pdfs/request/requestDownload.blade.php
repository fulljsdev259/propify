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

                                    <span>{{ $request->service_request_format }}</span> <br />

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
                                        @lang('models.request.status.'.\App\Models\Request::Status[$request->status]) ({{ now()->format('d.m.Y, H:i') }})
                                    </p>
                                    <p style="margin:7px 0 0;">
                                        <b>@lang('general.address'):</b>
                                        {{ @$contract->building->address->street }}
                                        {{ @$contract->building->address->house_num }},
                                        {{ @$contract->building->address->zip }}
                                        {{ @$contract->building->address->city }}

                                    </p>
                                </td>

                                <td style="text-align:right;vertical-align:top;" class="table_header">
                                    <img class="logo" src="{{public_path($logo)}}"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                                            @if( (!empty($category['capture_phase']) && $category['capture_phase'] == 1) || (!empty($subCategory['capture_phase']) && $subCategory['capture_phase'] == 1) )

                                                <td class="no_border">
                                                    <strong>
                                                        @lang('models.request.category_options.capture_phase'):
                                                    </strong>
                                                </td>

                                                <td class="no_border">
                                                    @if(key_exists($request->capture_phase, \App\Models\Request::CapturePhase))
                                                        @lang('models.request.sub_category_fields.capture_phase.' . \App\Models\Request::CapturePhase[$request->capture_phase])
                                                    @endif
                                                </td>

                                            @endif

                                            @if( (!empty($category['qualification']) && $category['qualification'] == 1) || (!empty($subCategory['qualification']) && $subCategory['qualification'] == 1) )

                                                <td class="no_border">
                                                    <strong>
                                                        @lang('models.request.qualification.label'):
                                                    </strong>
                                                </td>
                                                <td class="no_border">
                                                    @lang('models.request.qualification.'.\App\Models\Request::Qualification[$request->qualification])
                                                </td>

                                           @endif
                                        </tr>


                                        <tr>
                                            <td class="border_btm"><strong>@lang('models.request.category_options.component'):</strong></td>
                                            <td class="border_btm">{{ $request->component }}</td>



                                            @if( (!empty($category['location']) && $category['location'] == 1) || (!empty($subCategory['location']) && $subCategory['location'] == 1) )
                                                <td class="border_btm">
                                                    <strong> @lang('models.request.category_options.range'):</strong>
                                                </td>
                                                <td class="border_btm">
                                                    @if(key_exists($request->location, \App\Models\Request::Location))
                                                        @lang('models.request.sub_category_fields.location.' . \App\Models\Request::Location[$request->location]);
                                                    @endif
                                                </td>
                                            @endif

                                            @if( (!empty($category['room']) && $category['room'] == 1) || (!empty($subCategory['room']) && $subCategory['room'] == 1) )
                                                <td class="border_btm"><strong>@lang('models.request.category_options.room'):</strong></td>
                                                <td class="border_btm">
                                                    @if(key_exists($request->room, \App\Models\Request::Room))
                                                        @lang('models.request.sub_category_fields.room.' . \App\Models\Request::Room[$request->room]);
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
                    <p style="display:block;width:100%;margin-top:5px;">{!!  $request->description !!} </p>
                </td>
            </tr>
            <tr>
                <td class="no_border">
                    <h4 style="margin-bottom:0;font-size: 14px;">@lang('models.request.download_pdf.contact_details'):</h4>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="no_border" width="100%">
                    <p style="display:block;width:100%;margin-top:5px;">@lang('models.request.download_pdf.contact_text')</p>
                </td>
            </tr>
        <tr>
            <td colspan="2" width="100%" class="no_border">
                <table width="100%">
                <tbody>
                    <tr>
                        <td colspan="5" class="no_border" width="100%">
                            <table class="info_table" width="100%">
                                <tbody>
                                <tr>
                                    <td class="no_border"><strong>@lang('general.name'):</strong></td>
                                    <td class="no_border" width="163px">{{ $resident->user->name }}</td>

                                    <td class="no_border" ><strong>@lang('general.email'):</strong></td>
                                    <td  class="no_border">{{ $resident->user->email }}</td>
                                    <td class="no_border"></td>

                                </tr>

                               {{-- <tr>
                                    <td><strong>@lang('models.request.visibility.label',[],$language)</strong></td>
                                    <td>@lang('models.request.visibility.'.\App\Models\Request::Visibility[$request->visibility],[],$language)</td>

                                    <td><strong>@lang('models.request.priority.label',[],$language)</strong></td>
                                    <td>@lang('models.request.priority.'.\App\Models\Request::Priority[$request->priority],[],$language)</td>

                                </tr>--}}

                                <tr>
                                    @if($resident->mobile != null)
                                        <td class="border_btm"><strong>@lang('models.resident.mobile_phone'):</strong></td>
                                        <td class="border_btm">{{ $resident->mobile }}</td>
                                    @else
                                        <td class="border_btm"><strong>@lang('models.resident.private_phone'):</strong></td>
                                        <td class="border_btm">{{ $resident->private_phone }}</td>
                                    @endif

                                        <td class="border_btm"></td>
                                        <td class="border_btm"></td>
                                        <td class="border_btm"></td>


                                </tr>

                                <tr>
                                    <td colspan="4" class="no_border" width="100%" style="margin-top:50px;">
                                        <span style="margin-top:20px;border-bottom:2px dotted #888;padding-bottom:30px;display:inline-block;width:48%;float:left;">
                                            @lang('models.request.download_pdf.customer_signature')
                                        </span>
                                        <span style="margin-top:20px;border-bottom:2px dotted #888;padding-bottom:30px;display:inline-block;width:48%;float:right;">
                                            @lang('models.request.download_pdf.entrepreneur_signature')
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
        </tbody>
    </table>

@endsection
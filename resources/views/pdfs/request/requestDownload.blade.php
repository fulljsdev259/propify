@extends('pdfs.request.layout')
@section('title')
    @lang("models.request.download_pdf.service_request", [], $language)
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
                                        <b>@lang('models.request.category',[],$language):</b>
                                        {{ ($category->parentCategory != null) ? $category->parentCategory->{'name'.($language != 'en' ? '_'.$language : '') } . ' > ' : ''  }}
                                        {{ $category->{'name'.($language != 'en' ? '_'.$language : '') } }}
                                    </p>
                                    <p style="margin:7px 0 0;">
                                        <b>@lang('models.request.status.label',[],$language):</b>
                                        @lang('models.request.status.'.\App\Models\Request::Status[$request->status],[],$language) ({{ now()->format('d.m.Y, H:i') }})
                                    </p>
                                    <p style="margin:7px 0 0;">
                                        <b>@lang('general.address',[],$language):</b>
                                        {{ @$tenant->address->street }}
                                        {{ @$tenant->address->house_num }},
                                        {{ @$tenant->address->zip }}
                                        {{ @$tenant->address->city }}

                                    </p>
                                </td>

                                <td style="text-align:right;vertical-align:top;" class="table_header">
                                    <img class="logo" src="http://dev.propify.ch/storage/fortimo-ag-1.png"/>
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
                                            @if($category->acquisition == 1 || ($category->parentCategory != null && $category->parentCategory->acquisition))

                                            <td class="no_border">
                                                <strong>
                                                    @lang('models.request.category_options.acquisition',[],$language):
                                                </strong>
                                            </td>

                                            <td class="no_border">
                                                @if(key_exists($request->capture_phase, \App\Models\Request::CapturePhase))
                                                    @lang('models.request.sub_category_fields.capture_phase.' . \App\Models\Request::CapturePhase[$request->capture_phase], [], $language)
                                                @endif
                                            </td>

                                            @endif
                                            @if($category->has_qualifications == 1 || ($category->parentCategory != null && $category->parentCategory->has_qualifications))
                                                <td class="no_border">
                                                    <strong>
                                                        @lang('models.request.qualification.label',[],$language):
                                                    </strong>
                                                </td>
                                                <td class="no_border">
                                                    @lang('models.request.qualification.'.\App\Models\Request::Qualification[$request->qualification],[],$language)
                                                </td>
                                           @endif
                                        </tr>


                                        <tr>
                                            <td class="border_btm"><strong>@lang('models.request.category_options.component',[],$language):</strong></td>
                                            <td class="border_btm">{{ $request->component }}</td>



                                            @if($category->location == 1 || ($category->parentCategory != null && $category->parentCategory->location))
                                                <td class="border_btm">
                                                    <strong> @lang('models.request.category_options.range',[],$language):</strong>
                                                </td>
                                                <td class="border_btm">
                                                    @if(key_exists($request->location, \App\Models\Request::Location))
                                                        @lang('models.request.sub_category_fields.location.' . \App\Models\Request::Location[$request->location], [], $language);
                                                    @endif
                                                </td>
                                            @endif

                                            @if($category->room == 1 || ($category->parentCategory != null && $category->parentCategory->room))
                                             <td class="border_btm"><strong>@lang('models.request.category_options.room',[],$language):</strong></td>
                                                <td class="border_btm">
                                                    @if(key_exists($request->room, \App\Models\Request::Room))
                                                        @lang('models.request.sub_category_fields.room.' . \App\Models\Request::Room[$request->room], [], $language);
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
                    <h4 style="margin-bottom:0;font-size: 14px;">@lang('general.title',[],$language):</h4>
                    <p style="display:block;width:100%;margin-top:5px;">{{ $request->title }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="no_border" width="100%">
                    <h4 style="margin-bottom:0;font-size: 14px;">@lang('general.description',[],$language):</h4>
                    <p style="display:block;width:100%;margin-top:5px;">{!!  $request->description !!} </p>
                </td>
            </tr>
            <tr>
                <td class="no_border">
                    <h4 style="margin-bottom:0;font-size: 14px;">@lang('models.request.download_pdf.contact_details',[],$language):</h4>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="no_border" width="100%">
                    <p style="display:block;width:100%;margin-top:5px;">@lang('models.request.download_pdf.contact_text',[],$language)</p>
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
                                    <td class="no_border"><strong>@lang('general.name',[],$language):</strong></td>
                                    <td class="no_border" width="163px">{{ $tenant->user->name }}</td>

                                    <td class="no_border" ><strong>@lang('general.email',[],$language):</strong></td>
                                    <td  class="no_border">{{ $tenant->user->email }}</td>
                                    <td class="no_border"></td>

                                </tr>

                               {{-- <tr>
                                    <td><strong>@lang('models.request.visibility.label',[],$language)</strong></td>
                                    <td>@lang('models.request.visibility.'.\App\Models\Request::Visibility[$request->visibility],[],$language)</td>

                                    <td><strong>@lang('models.request.priority.label',[],$language)</strong></td>
                                    <td>@lang('models.request.priority.'.\App\Models\Request::Priority[$request->priority],[],$language)</td>

                                </tr>--}}

                                <tr>
                                    @if($tenant->mobile != null)
                                        <td class="border_btm"><strong>@lang('models.tenant.mobile_phone',[],$language):</strong></td>
                                        <td class="border_btm">{{ $tenant->mobile }}</td>
                                    @else
                                        <td class="border_btm"><strong>@lang('models.tenant.private_phone',[],$language):</strong></td>
                                        <td class="border_btm">{{ $tenant->private_phone }}</td>
                                    @endif

                                        <td class="border_btm"></td>
                                        <td class="border_btm"></td>
                                        <td class="border_btm"></td>


                                </tr>

                                <tr>
                                    <td colspan="4" class="no_border" width="100%" style="margin-top:50px;">
                                        <span style="margin-top:20px;border-bottom:2px dotted #888;padding-bottom:30px;display:inline-block;width:48%;float:left;">
                                            @lang('models.request.download_pdf.customer_signature',[],$language)
                                        </span>
                                        <span style="margin-top:20px;border-bottom:2px dotted #888;padding-bottom:30px;display:inline-block;width:48%;float:right;">
                                            @lang('models.request.download_pdf.entrepreneur_signature',[],$language)
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
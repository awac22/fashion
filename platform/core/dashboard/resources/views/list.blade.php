@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div id="dashboard-alerts">
        <verify-license-component verify-url="{{ route('settings.license.verify') }}" setting-url="{{ route('settings.options') }}"></verify-license-component>
        @if (config('core.base.general.enable_system_updater') && Auth::user()->isSuperUser())
            <check-update-component check-update-url="{{ route('system.check-update') }}" setting-url="{{ route('system.updater') }}"></check-update-component>
        @endif
    </div>
    {!! apply_filters(DASHBOARD_FILTER_ADMIN_NOTIFICATIONS, null) !!}
    <div class="row">
        {!! apply_filters(DASHBOARD_FILTER_TOP_BLOCKS, null) !!}
    </div>
    <div class="clearfix"></div>
	    <div class="row">
										<div  class="col-md-12 mt-3  mb-3  mt-md-0">
											<select style="color:#000;width:200px;border:1px solid #000;"  onchange="admin(this.value);" name="admin" class="admin_change btn btn-outline-light"> 
												<option value="">Select Admin</option>
												<option value="https://anywhereanycity.com/home/login/admin?e=<?php echo Auth::user()->email; ?>">Home</option>
												<option value="https://anywhereanycity.com/awactv?e=<?php echo Auth::user()->email; ?>">AWACTV</option>
												<option value="https://awacradio.anywhereanycity.com/?e=<?php echo Auth::user()->email; ?>">AWACRADIO</option>
												<option value="https://anywhereanycity.com/art/?e=<?php echo Auth::user()->email; ?>">Art</option>
												<option value="https://anywhereanycity.com/gallery/?e=<?php echo Auth::user()->email; ?>">Gallery</option>
												<option value="https://events.anywhereanycity.com/?e=<?php echo Auth::user()->email; ?>">Events</option>
												<option value="https://anywhereanycity.com/fashion/?e=<?php echo Auth::user()->email; ?>">Fashion</option>
												<option value="https://anywhereanycity.com/marketplace/?e=<?php echo Auth::user()->email; ?>">Marketplace</option>
												<option value="https://anywhereanycity.com/network/?e=<?php echo Auth::user()->email; ?>">Network</option>
												<option value="https://anywhereanycity.com/support/?e=<?php echo Auth::user()->email; ?>">Support</option>
											
											</select>
										</div> 
									</div> 
	
    <div class="row">
        @foreach ($statWidgets as $widget)
            {!! $widget !!}
        @endforeach
        <div class="clearfix"></div>
    </div>
    <div id="list_widgets" class="row">
        @foreach ($userWidgets as $widget)
            {!! $widget !!}
        @endforeach
        <div class="clearfix"></div>
    </div>

    @if (count($userWidgets) > 0)
        <a href="#" class="manage-widget"><i class="fa fa-plus"></i> {{ trans('core/dashboard::dashboard.manage_widgets') }}</a>
        @include('core/dashboard::partials.modals', compact('widgets'))
    @endif

@stop
<script type="text/javascript">		
	 
	 function admin( value){
		 
		  location.href = value;
	 }
	 
	 


</script> 
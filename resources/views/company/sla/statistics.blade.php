
<div style="display: flex; padding: 10px 10px ; margin-bottom: 20px; align-items: center; justify-content: center; gap: 20px;">
    <div class="tab-sla {{  empty(request('view')) || request('view') == 'work-order-open' ? 'classWithShadow' : '' }}"  style="cursor: pointer; background-color: #f0ad4e; color:white; width: 20% !important;" data-value="work-order-open">
      <div class="tab-sla-nmbr">{{  count($workOrderOpen) }}</div>
      <div style="padding-left: 3px;" >Work Order Open</div>
    </div>
    <div class="tab-sla {{  request('view') == 'work-order-close' ? 'classWithShadow' : '' }}" style="cursor: pointer; background-color: #286090; color:white; width: 20% !important; " data-value="work-order-close"><div class="tab-sla-nmbr">{{  count($workOrderClosed) }}</div>
      <div style="padding-left: 3px;" >Work Order Closed</div></div>
    <div class="tab-sla {{  request('view') == 'response-sla-breach' ? 'classWithShadow' : '' }}" style="cursor: pointer;  background-color: #449d44; color:white; width: 20% !important;" data-value="response-sla-breach"><div class="tab-sla-nmbr">{{  count($breachResponseTime) }}</div>
      <div style="padding-left: 3px;" onclick="gettabel('table-3');">Response SLA Breach</div></div>
    <div class="tab-sla {{  request('view') == 'assessment-sla-breach' ? 'classWithShadow' : '' }}" style="cursor: pointer;  background-color: #31b0d5; color:white; width: 20% !important;" data-value="assessment-sla-breach"><div class="tab-sla-nmbr">{{  count($breachAssessmentTime) }}</div>
      <div style="padding-left: 3px;" onclick="gettabel('table-4');">Assessment SLA Breach</div></div>
      <div class="tab-sla {{  request('view') == 'rectification-sla-breach' ? 'classWithShadow' : '' }}" style="cursor: pointer; background-color: #d9534f; color:white; width: 20% !important;" data-value="rectification-sla-breach"><div class="tab-sla-nmbr">{{  count($breachRectificationTime) }}</div>
      <div style="padding-left: 3px;" onclick="gettabel('table-5');">Rectification SLA Breach</div></div>
  </div>

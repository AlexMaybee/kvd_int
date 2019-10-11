{"version":3,"sources":["gantt-view.js"],"names":["tasksListNS","approveTask","taskId","ganttChart","updateTask","status","dateCompleted","Date","SetServerStatus","bGannt","disapproveTask","CloseTask","StartTask","AcceptTask","PauseTask","RenewTask","DeferTask","AddToFavorite","parameters","data","mode","add","sessid","BX","message","id","ajax","method","dataType","url","tasksListAjaxUrl","processData","onsuccess","datum","DeleteFavorite","rowDelete","TASKS_table_view_onDeleteClick_onSuccess","DeleteTask","toString","trim","length","removeTask","onCustomEvent","onPopupTaskChanged","task","__RenewMenuItems","__InvalidateMenus","parentTaskId","parentTask","getTaskById","hasChildren","expand","projectId","getProjectById","project","addProjectFromJSON","name","projectName","opened","canCreateTasks","projectCanCreateTasks","canEditTasks","projectCanEditTasks","counterUpdate","filterId","ganttFilterId","filterObject","Main","filterManager","getById","fields","getFilterFieldsValues","roleid","ROLEID","addCustomEvent","window","onPopupTaskAdded","addTaskFromJSON","onPopupTaskDeleted","lastScroll","onBeforeShow","browser","IsOpera","layout","timeline","scrollLeft","onAfterShow","onBeforeHide","onAfterHide","quickInfoData","clone","menuItems","__FilterMenuByStatus"],"mappings":"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","file":""}
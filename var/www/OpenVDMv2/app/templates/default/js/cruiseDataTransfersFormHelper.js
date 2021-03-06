$(function () {
    'use strict';
    
    var transferTypeOptions = [
        {"value" : "1", "text" : "Local Directory"},
        {"value" : "2", "text" : "Rsync Server"},
        {"value" : "3", "text" : "SMB Share"},
        {"value" : "4", "text" : "SSH Server"},
        {"value" : "5", "text" : "NFS Share"}
    ];
    
    function setTransferTypeFields(transferType) {

        if (transferType === '') { transferType = '1'; }
        var transferTypeText = transferTypeOptions[parseInt(transferType, 10) - 1].text;
        
        switch (transferTypeText) {
        case "Local Directory":
            $(".rsyncServer").hide();
            $(".smbShare").hide();
            $(".sshServer").hide();
            $(".nfsShare").hide();
            break;
        case "Rsync Server":
            $(".rsyncServer").show();
            $(".smbShare").hide();
            $(".sshServer").hide();
            $(".nfsShare").hide();
            break;
        case "SMB Share":
            $(".rsyncServer").hide();
            $(".smbShare").show();
            $(".sshServer").hide();
            $(".nfsShare").hide();
            break;
        case "SSH Server":
            $(".rsyncServer").hide();
            $(".smbShare").hide();
            $(".sshServer").show();
            $(".nfsShare").hide();
            break;
        case "NFS Share":
            $(".rsyncServer").hide();
            $(".smbShare").hide();
            $(".sshServer").hide();
            $(".nfsShare").show();
            break;
        default:
        }
    }
        
    setTransferTypeFields($('input[name=transferType]:checked').val());
    
    $('input[name=transferType]').change(function () {
        setTransferTypeFields($(this).val());
    });
    
});

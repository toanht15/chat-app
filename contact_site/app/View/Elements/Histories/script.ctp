<script type="text/javascript">
function openSearchRefine(){
  $.ajax({
    type: 'post',
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/Histories/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-thistory-entry', '絞り込み検索', 'moment');
    }
  });
}

function SaveToFile(FileName, Stream) {
  if (window.navigator.msSaveBlob) {
    window.navigator.msSaveBlob(new Blob([Stream], { type: "text/plain" }), FileName);
  } else {
    console.log('huhuhuhu');
    var a = document.createElement("a");
    a.href = URL.createObjectURL(new Blob([Stream], { type: "text/plain" }));
    //a.target   = '_blank';
    a.download = FileName;
    document.body.appendChild(a) //  Firefox specification
    a.click();
    document.body.removeChild(a) //  Firefox specification
  }
}

function run() {
  var Stream = 'HElloWorld!';
  SaveToFile('test.txt', Stream);
}
</script>

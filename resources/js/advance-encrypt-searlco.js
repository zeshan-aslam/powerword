let SearlcoEncryptionApiKey ='';
var SearlcoEncryptionLink = window.levenshtein || {};
var SearlcoEncryptionWords = [];
var SearlcoEncryptionDate = Date.parse(new Date());
var SearlcoEncryptionApiCount = 0;
var SearlcoEncryptionApiData = [];
var SearlcoEncryptionLocalURl = "http://127.0.0.1:8000/api/";
var SearlcoEncryptionProductionUrl = "https://performanceaffiliate.com/poweredWordsAPIs/public/api/";

class levenshtein {

  constructor() {
    // this.getData();
  }

  getData() {
    let vm = this
    fetch(SearlcoEncryptionProductionUrl+"user",{headers: {
      'Content-Type': 'application/json',
      'Api-Key': levenshteinKey
    }})
      .then((response) => response.json())
      .then((data) => {
        data.data.forEach(function (value, index) {
        console.log(value, index);
        vm.findText(value.i, value.w) 
        });
     
      });
  }


  findText(link,word) {
    setTimeout(function () {
      let  searched= word;
      
      if (searched !== "") {
          var search = word;
          let urlData = {
            link:link,
            word:word
          };
          let uData = JSON.parse(JSON.stringify(urlData));
          
          var replacement = `<a href="${SearlcoEncryptionProductionUrl}saveDetail?id=${link}&key=${levenshteinKey}">${word}</a>`;
          
			    const regex = new RegExp("(\\b"+word+"\\b)(?!(?:(?!<\/?a\b[^>]*>).)*?<\/a>)(?!(?:(?!<\/?img\b[^>]*>).)*?\/>)", 'g');
									  
			
           document.body.innerHTML = document.body.innerHTML.replace(regex,replacement);  ;

           
      }
    }, 10);
  }
}

var datep = SearlcoEncryptionDate;

console.log(new Date())
const lev = new levenshtein();
lev.getData()



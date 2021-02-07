<script>
let dxfunctions = {    
    updateExpenses(id, cost, options, note){        
        let request = axios.post(`/update/expenses/${id}`, {                    
            amount: cost,
            tender: options,
            note: note,
        }).then( (response) =>{
            return response.data;
        }).catch(err => console.log(err))

        request.then(result=>{   
            console.log(result.amount)    
            return result.amount;         
            if(typeof result.amount === "undefined"){
                // Success
                this.amountError = false;
                this.success = true;
            }else{
                // Error                        
                this.amountError = true;
            }
        })
    },
    testme(val){
        console.log(val);
    }
}

export default dxfunctions;
</script>
import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { HttpClient } from '@angular/common/http';
import { RestResponse } from '../../models/rest.response';
import { ApiService } from '../../services/api.service';
import { EcoleModel } from '../../models/ecole.model';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';

@Component({
  selector: 'app-form-user',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, FootComponent, NavComponent, CommonModule, FormsModule, ReactiveFormsModule],
  templateUrl: './form-user.component.html',
  styleUrl: './form-user.component.css'
})
export class FormUserComponent implements OnInit{
  profileForm:FormGroup;
  ecoleResponse?:RestResponse<EcoleModel[]>;
  addReturnResponse:any;
  profiles:number = 0;
  error:boolean = false;
  ecoleId:number = 0;
  op2:boolean = false;
  user?:LogUser
  constructor(private router:Router, private http:HttpClient, private authService:AuthServiceImpl, private formBuilder: FormBuilder, private apiService:ApiService, private ecoleService:EcoleServiceImpl) 
  {
    this.profileForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
      ecole: ['', Validators.required],
      option1: false,
      option2: false,
      option3: false,
    });
  }
  
  ngOnInit(): void { 
    this.user = this.authService.getUser()
    if(this.user?.role != 'ROLE_ADMIN'){
      this.router.navigate(['/app/not-found'])
    }

    this.ecoleService.findAll().subscribe(data=>this.ecoleResponse = data); 
    this.profileForm.valueChanges.subscribe(value => {
      if (typeof window !== 'undefined' && window.localStorage) {
        localStorage.setItem('profileForm', JSON.stringify(this.profileForm.value));
      }
      this.ecoleId = this.profileForm.get('ecole')?.value;
      this.op2 = this.profileForm.get('option2')?.value;
      //console.log(this.ecoleId, this.op2)
    });
    
    console.log(this.profileForm.value);
  }

  get emailControl() {
    return this.profileForm.get('email');
  }

  onSubmit() {
    console.log(this.profileForm.value);

    if (typeof window !== 'undefined' && localStorage){
      const profileForm = localStorage.getItem('profileForm');
      this.addReturnResponse = profileForm? JSON.parse(profileForm) : []

      this.apiService.sendNewUserToBack(this.addReturnResponse).subscribe(
        response => {
          console.log('Data successfully sent', response);
          this.clearData();
          this.router.navigate(['/app/users']);
        },
        error => {
          console.error('Error sending data', error);
          this.error = true;
        }
      )
    }else{
      this.error = true;
    }
  }

  clearData(){
    if (typeof window !== 'undefined' && localStorage){
      localStorage.removeItem('profileForm');
    }
  }
}

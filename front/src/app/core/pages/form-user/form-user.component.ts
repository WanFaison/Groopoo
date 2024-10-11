import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { HttpClient } from '@angular/common/http';
import { RestResponse } from '../../models/rest.response';
import { ProfileModel } from '../../models/profile.model';
import { ProfileServiceImpl } from '../../services/impl/profile.service.impl';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-form-user',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, FootComponent, NavComponent, CommonModule, FormsModule, ReactiveFormsModule],
  templateUrl: './form-user.component.html',
  styleUrl: './form-user.component.css'
})
export class FormUserComponent implements OnInit{
  profileResponse?:RestResponse<ProfileModel[]>;
  profileForm:FormGroup;
  addReturnResponse:any;
  profiles:number = 0;
  error:boolean = false;
  email:string ='';
  constructor(private router:Router, private http:HttpClient, private formBuilder: FormBuilder, private apiService:ApiService, private profileService:ProfileServiceImpl) 
  {
    this.profileForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
      option1: false,
      option2: false,
      option3: false,
    });
  }
  
  ngOnInit(): void {
    this.profileService.findAll().subscribe(data=>this.profileResponse = data)
    if(this.profileResponse){
      this.profiles = this.profileResponse?.results.length
    }
    
    this.profileForm.valueChanges.subscribe(value => {
      if (typeof window !== 'undefined' && window.localStorage) {
        localStorage.setItem('profileForm', JSON.stringify(this.profileForm.value));
      }
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

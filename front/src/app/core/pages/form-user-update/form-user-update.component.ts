import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { HttpClient } from '@angular/common/http';
import { ApiService } from '../../services/api.service';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { LogUser } from '../../models/user.model';
import { response } from 'express';
import { ReturnResponse } from '../../models/return.model';
import { RequestResponse } from '../../models/rest.response';

@Component({
  selector: 'app-form-user-update',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, FootComponent, NavComponent, CommonModule, FormsModule, ReactiveFormsModule],
  templateUrl: './form-user-update.component.html',
  styleUrl: './form-user-update.component.css'
})
export class FormUserUpdateComponent implements OnInit{
  updateForm:FormGroup;
  usrnm:string = ''
  passwd1:string = ''
  passwd2:string = ''
  user?:LogUser;
  errorMsg:string = ''
  error:boolean = false;
  constructor(private router:Router, private formBuilder: FormBuilder, private authService:AuthServiceImpl, private http:HttpClient, private apiService:ApiService){
    this.user = this.authService.getUser();
    this.updateForm = this.formBuilder.group({
      username: [this.user?.username, Validators.required],
      pswd1: ['', Validators.required],
      pswd2: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  ngOnInit(): void {
    this.user = this.authService.getUser();
    this.updateForm.valueChanges.subscribe(value =>{
      if (typeof window !== 'undefined' && window.localStorage) {
        localStorage.setItem('updateForm', JSON.stringify(this.updateForm.value));
      }
      this.usrnm = this.updateForm.get('username')?.value;
      this.passwd1 = this.updateForm.get('pswd1')?.value;
      this.passwd2 = this.updateForm.get('pswd2')?.value;
    })
  }

  onSubmit() {
    //console.log(this.updateForm.value);

    if (typeof window !== 'undefined' && localStorage){
      const updateForm = localStorage.getItem('updateForm');
      const returnResponse = updateForm? JSON.parse(updateForm) : []

      if(this.user){
        this.apiService.sendModifUserToBack(this.user?.id, returnResponse).subscribe(
          (response:RequestResponse) =>{
            console.log(response);
            if(response.data != 0){
              this.error = true
              this.errorMsg = response.message
            }else{
              this.clearData();
              this.authService.logout();
              this.router.navigate(['/authentification/login']);
            }
          },
          error => {
            console.error('Error sending data', error);
            this.error = true;
          }
        )
      }
      
    }
  }

  clearData(){
    if (typeof window !== 'undefined' && localStorage){
      localStorage.removeItem('updateForm');
    }
  }

}

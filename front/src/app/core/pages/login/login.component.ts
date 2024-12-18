import { Component, OnInit } from '@angular/core';
import { Router, RouterLink, RouterLinkActive, RouterModule } from '@angular/router';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [RouterModule, CommonModule, FormsModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent implements OnInit{
  username: string = '';
  password: string = '';
  showPassword: boolean = false;
  errorMessage: any
  error:boolean = false

  constructor(private authService:AuthServiceImpl, private router:Router){}

  ngOnInit(): void {
    this.authService.logout()
  }

  togglePswd(): void {
    this.showPassword = !this.showPassword;
  }

  getCurrentYear(): number {
    const today = new Date();
    return today.getFullYear();
  }

  onLogin(): void {
    this.authService.login(this.username, this.password).subscribe(data=>{
      if(data.status==200){
        //console.log(data)
        localStorage.setItem('jwtToken', data.token);
        this.authService.setToken(data.token)
        this.authService.setUser(data.user)
        this.router.navigateByUrl("/app/home")
      }else{
         this.errorMessage=data
      }
    },
    error => {
      console.error(error);
      this.error = true;
    });
  }

}
